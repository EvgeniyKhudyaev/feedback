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
use App\Service\Feedback\FeedbackTargetManager;
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
        private readonly FeedbackEditorManager $feedbackEditorManager,
        private readonly FeedbackTargetManager $feedbackTargetManager,
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
        $targetServices = $this->serviceRepository->findTargetServicesByFeedback($feedback);

        return $this->render('admin/feedback/view.html.twig', [
            'feedback' => $feedback,
            'types' => FeedbackTypeEnum::getChoices(),
            'scopes' => FeedbackScopeEnum::getChoices(),
            'statuses' => StatusEnum::getChoices(),
            'timesCompleted' => $timesCompleted,
            'targetServices' => $targetServices
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}/edit', name: 'admin_feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedback $feedback): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$feedback->hasEditor($this->security->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $feedback->normalizeFieldsSortOrder();
                $this->em->persist($feedback);
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
    #[Route('/{id}/editors', name: 'admin_feedback_manager_editors', methods: ['GET'])]
    public function getManagerEditorsForm(Feedback $feedback): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$feedback->hasEditor($this->security->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $managers = $this->userRepository->findByRole(UserRoleEnum::MANAGER);

        return $this->render('admin/feedback/_manage_editors_modal.html.twig', [
            'feedback' => $feedback,
            'managers' => $managers,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}/editors', name: 'admin_feedback_manager_editor', methods: ['POST'])]
    public function managerEditorsSave(
        Feedback               $feedback,
        Request $request
    ): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_ADMIN') && !$feedback->hasEditor($this->security->getUser())) {
                throw $this->createAccessDeniedException();
            }

            $userIds = $request->request->all('managers');
            $this->feedbackEditorManager->updateEditors($feedback, $userIds);
            $this->em->flush();

            return new JsonResponse([
                'success' => true,
                'editors' => array_map(fn($editor) => [
                    'id' => $editor->getId(),
                    'name' => $editor->getEditor()->getName(),
                ], $feedback->getActiveEditors()->toArray()),
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
    #[Route('/{id}/relations', name: 'admin_feedback_relations', methods: ['GET'])]
    public function getRelationsForm(Feedback $feedback): Response
    {
        if (!$this->security->isGranted('ROLE_ADMIN') && !$feedback->hasEditor($this->security->getUser())) {
            throw $this->createAccessDeniedException();
        }

        $services = $this->serviceRepository->findAll();

        return $this->render('admin/feedback/_relations_form.html.twig', [
            'feedback' => $feedback,
            'services' => $services,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{id}/relations', name: 'admin_feedback_relation', methods: ['POST'])]
    public function relationsSave(
        Feedback $feedback,
        Request  $request,
    ): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_ADMIN') && !$feedback->hasEditor($this->security->getUser())) {
                throw $this->createAccessDeniedException();
            }

            $relationsIds = $request->request->all('relations');
            $this->feedbackTargetManager->updateTargets($feedback, $relationsIds);
            $this->em->flush();

            return new JsonResponse([
                'success' => true,
                'relations' => array_map(fn($editor) => [
                    'id' => $editor->getId(),
                    'name' => $editor->getEditor()->getName(),
                ], $feedback->getActiveEditors()->toArray()),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Ошибка сохранения связей: ' . $e->getMessage(), ['exception' => $e]);

            return new JsonResponse([
                'success' => false,
                'message' => 'Ошибка при сохранении. Попробуйте позже.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}