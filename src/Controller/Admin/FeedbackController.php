<?php

namespace App\Controller\Admin;

use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackField;
use App\Enum\Feedback\FeedbackScopeEnum;
use App\Enum\Feedback\FeedbackTypeEnum;
use App\Enum\Shared\StatusEnum;
use App\Form\Feedback\FeedbackType;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin/feedbacks')]
class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly FeedbackRepository $feedbackRepository,
    )
    {
    }

    #[Route('/', name: 'admin_feedback_index')]
    public function index(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $qb = $em->getRepository(Feedback::class)->createQueryBuilder('f');

        // Фильтр по ID
        $filterId = $request->query->get('filter_id');
        if ($filterId) {
            $qb->andWhere('f.id = :id')->setParameter('id', $filterId);
        }

        // Фильтр по имени
        $filterName = $request->query->get('filter_name');
        if ($filterName) {
            $qb->andWhere('f.name LIKE :name')->setParameter('name', '%'.$filterName.'%');
        }

        // Фильтр по типу
        $filterType = $request->query->get('filter_type');
        if ($filterType) {
            $qb->andWhere('f.type = :type')->setParameter('type', $filterType);
        }

        // Фильтр по области
        $filterScope = $request->query->get('filter_scope');
        if ($filterScope) {
            $qb->andWhere('f.scope = :scope')->setParameter('scope', $filterScope);
        }

        // Фильтр по статусу
        $filterStatus = $request->query->get('filter_status');
        if ($filterStatus) {
            $qb->andWhere('f.status = :status')->setParameter('status', $filterStatus);
        }

        // Сортировка
        $sort = $request->query->get('sort', 'f.id');
        $direction = strtoupper($request->query->get('direction', 'ASC'));
        $direction = in_array($direction, ['ASC', 'DESC']) ? $direction : 'ASC';

        // Допускаемые поля сортировки
        $allowedSortFields = ['f.id', 'f.name', 'f.type', 'f.scope', 'f.status', 'f.createdAt', 'f.updatedAt'];
        $sort = in_array($sort, $allowedSortFields) ? $sort : 'f.id';

        $qb->orderBy($sort, $direction);

        // Пагинация
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );
        

        return $this->render('admin/feedback/index.html.twig', [
            'pagination'    => $pagination,
            'filterId'      => $filterId,
            'filterName'    => $filterName,
            'filterType'    => $filterType,
            'filterScope'   => $filterScope,
            'filterStatus'  => $filterStatus,
            'sort'          => $sort,
            'direction'     => $direction,
            'types'         => FeedbackTypeEnum::getChoices(),
            'scopes'        => FeedbackScopeEnum::getChoices(),
            'statuses'      => StatusEnum::getChoices(),
        ]);
    }

    #[Route('/{id}', name: 'admin_feedback_view', requirements: ['id' => '\d+'])]
    public function view(Feedback $feedback): Response
    {
        return $this->render('admin/feedback/view.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/new', name: 'admin_feedback_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $feedback = new Feedback();

        $form = $this->createForm(FeedbackType::class, $feedback, [
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($feedback);
            $em->flush();

            $this->addFlash('success', 'Опрос успешно создан.');

            return $this->redirectToRoute('admin_feedback_index');
        }

        return $this->render('admin/feedback/new.html.twig', [
            'form' => $form->createView(),
            'field_prototype' => $form->createView()->children['fields']->vars['prototype'],
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Feedback $feedback): Response
    {
        // Редактирование опросника
    }
}