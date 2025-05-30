<?php

namespace App\Controller\Admin;

use App\DTO\User\UserFilterDto;
use App\DTO\User\UserSortDto;
use App\Entity\User;
use App\Enum\Shared\StatusEnum;
use App\Enum\UserRoleEnum;
use App\Form\User\UserEditType;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService    $userService
    )
    {
    }

    #[Route('/', name: 'admin_user_index')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $filters = new UserFilterDto($request->query->all());
        $sort = new UserSortDto($request->query->all());

        $qb = $this->userRepository->getFilteredQueryBuilder($filters);
//        $qb = $this->userRepository->applySorting($qb, $sort);

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
            'sort' => $sort,
            'statuses'      => StatusEnum::getChoices(),
            'roles'      => UserRoleEnum::getChoicesIndex(),
        ]);
    }

    #[Route('/{id}', name: 'admin_user_view', requirements: ['id' => '\d+'])]
    public function view(User $user): Response
    {
        return $this->render('admin/user/view.html.twig', [
            'user' => $user,
            'statuses'      => StatusEnum::getChoices(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->userRepository->save($user);

                $this->addFlash('success', 'Пользователь успешно обновлен.');

                return $this->redirectToRoute('admin_user_view', [
                    'id' => $user->getId(),
                ]);
            }

            $this->addFlash('danger', 'Форма содержит ошибки. Пожалуйста, проверьте введённые данные.');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_user_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if (!$this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Ошибка проверки безопасности.');

            return $this->redirectToRoute('admin_user_index');
        }

        try {
            $this->userService->markAsDeleted($user);
            $this->addFlash('success', 'Пользователь успешно удалён.');
        } catch (\DomainException $e) {
            // Если есть логика DomainException для бизнес-ошибок
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            // Общий catch для непредвиденных ошибок
            $this->addFlash('error', 'Произошла ошибка при удалении пользователя.');
            // Опционально можно залогировать ошибку
            // $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return $this->redirectToRoute('admin_user_index');
    }
}