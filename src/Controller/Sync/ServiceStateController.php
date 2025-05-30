<?php

namespace App\Controller\Sync;

use App\Enum\Shared\StatusEnum;
use App\Repository\ServiceRepository;
use App\Repository\ServiceStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sync/service-states')]
class ServiceStateController extends AbstractController
{

    public function __construct(
        private readonly ServiceStatusRepository $serviceStatusRepository,
        private readonly EntityManagerInterface $em,

    ) {
    }

    #[Route('/index', name: 'sync_service_state_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $qb = $this->serviceStatusRepository->createQueryBuilder('s');

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('sync/service_state_index.html.twig', [
            'pagination' => $pagination,
            'statuses'      => StatusEnum::getChoices(),

        ]);
    }

    #[Route('/{serviceState}/view', name: 'sync_service_state_view', methods: ['GET'])]
    public function view(int $id)
    {

    }
}