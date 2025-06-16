<?php

namespace App\Controller\Admin;

use App\DTO\Feedback\FeedbackFilterDto;
use App\DTO\Feedback\FeedbackSortDto;
use App\Entity\Feedback\Feedback;
use App\Enum\Feedback\FeedbackScopeEnum;
use App\Enum\Feedback\FeedbackTypeEnum;
use App\Enum\Shared\StatusEnum;
use App\Enum\UserRoleEnum;
use App\Form\Feedback\FeedbackType;
use App\Repository\FeedbackFieldAnswerRepository;
use App\Repository\FeedbackRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use App\Service\Feedback\FeedbackEditorManager;
use App\Service\Feedback\FeedbackService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/feedbacks')]
class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly FeedbackRepository $feedbackRepository,
        private readonly UserRepository  $userRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly FeedbackFieldAnswerRepository $feedbackFieldAnswerRepository,
        private readonly FeedbackService               $feedbackService,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    #[Route('/', name: 'admin_feedback_index')]
    public function index(Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$this->security->isGranted('ROLE_MANAGER')) {
            throw $this->createAccessDeniedException();
        }

        $filters = new FeedbackFilterDto($request->query->all());
        $sort = new FeedbackSortDto($request->query->all());
        $pagination = $this->feedbackService->getFilteredPagination($filters, $sort, $request->query->getInt('page', 1));

        return $this->render('admin/feedback/index.html.twig', [
            'pagination'    => $pagination,
            'filters'       => $filters,
            'sort'          => $sort,
            'types'         => FeedbackTypeEnum::getChoices(),
            'scopes'        => FeedbackScopeEnum::getChoices(),
            'statuses'      => StatusEnum::getChoices(),
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}', name: 'admin_feedback_view', requirements: ['id' => '\d+'])]
    public function view(Feedback $feedback): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$feedback->hasEditor($this->security->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $timesCompleted = $this->feedbackFieldAnswerRepository->countUniqueClientsForFeedback($feedback->getId());

        return $this->render('admin/feedback/view.html.twig', [
            'feedback' => $feedback,
            'types' => FeedbackTypeEnum::getChoices(),
            'scopes' => FeedbackScopeEnum::getChoices(),
            'statuses' => StatusEnum::getChoices(),
            'timesCompleted' => $timesCompleted,
        ]);
    }

    #[Route('/create', name: 'admin_feedback_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$this->security->isGranted('ROLE_MANAGER')) {
            throw $this->createAccessDeniedException();
        }

        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $feedback->normalizeFieldsSortOrder();
                $this->em->persist($feedback);
                $this->em->flush();

                $this->addFlash('success', 'Опрос успешно создан.');

                return $this->redirectToRoute('admin_feedback_index');
            } catch (\Exception $e) {
                $this->logger->error('Ошибка при сохранении опроса: ' . $e->getMessage());
                $this->addFlash('danger', 'Произошла ошибка при сохранении опроса. Попробуйте позже.');
            }
        }

        return $this->render('admin/feedback/create.html.twig', [
            'form' => $form->createView(),
            'field_prototype' => $form->createView()->children['fields']->vars['prototype'],
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedback $feedback): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$this->security->isGranted('ROLE_MANAGER')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $feedback->normalizeFieldsSortOrder();
                $this->em->persist($feedback); // persist необязателен для уже существующего объекта, но можно оставить
                $this->em->flush();

                $this->addFlash('success', 'Опрос успешно обновлён.');

                return $this->redirectToRoute('admin_feedback_index');
            } catch (\Exception $e) {
                $this->logger->error('Ошибка при сохранении опроса: ' . $e->getMessage());
                $this->addFlash('danger', 'Произошла ошибка при сохранении опроса. Попробуйте позже.');
            }
        }

        return $this->render('admin/feedback/edit.html.twig', [
            'form' => $form->createView(),
            'field_prototype' => $form->createView()->children['fields']->vars['prototype'],
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}/editors', name: 'admin_feedback_manage_editors', methods: ['GET'])]
    public function manageEditorsForm(Feedback $feedback): Response
    {
        $managers = $this->userRepository->findByRole(UserRoleEnum::MANAGER);

        return $this->render('admin/feedback/_manage_editors_modal.html.twig', [
            'feedback' => $feedback,
            'managers' => $managers,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}/editors', name: 'admin_feedback_manage_editor', methods: ['POST'])]
    public function manageEditorsSave(
        Feedback               $feedback,
        Request                $request,
        FeedbackEditorManager  $editorManager,
    ): Response
    {
        try {
            $selectedUserIds = $request->request->all('managers');
            $editorManager->updateEditors($feedback, $selectedUserIds);
            $this->em->flush();

            return new JsonResponse([
                'success' => true,
                'editors' => array_map(fn($editor) => [
                    'id' => $editor->getId(),
                    'name' => $editor->getEditor()->getName(),
                ], $feedback->getActiveFeedbackEditors()->toArray()),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Ошибка сохранения менеджеров: ' . $e->getMessage(), ['exception' => $e]);

            return new JsonResponse([
                'success' => false,
                'message' => 'Ошибка при сохранении. Попробуйте позже.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}/relations', name: 'admin_feedback_relations', requirements: ['id' => '\d+'])]
    public function getRelationsForm(Feedback $feedback): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // или другая логика доступа

        $services = $this->serviceRepository->findAll(); // загрузить сервисы

        return $this->render('admin/feedback/_relations_form.html.twig', [
            'feedback' => $feedback,
            'services' => $services,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}/manage-relations', name: 'admin_feedback_manage_relations', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function manageRelations(Request $request, Feedback $feedback): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $relationsIds = $request->request->get('relations', []);

        // логика сохранения связей (например, $feedback->setFieldsByIds($relationsIds))

        $this->em->flush();

        // вернуть актуальные связи для обновления списка на странице
        $relations = [];
        foreach ($feedback->getFields() as $field) {
            $relations[] = [
                'name' => $field->getLabel(),
                'value' => $field->get(),
            ];
        }

        return $this->json([
            'success' => true,
            'relations' => $relations,
        ]);
    }
}