<?php

namespace App\Controller\Sync;

use App\Entity\Feedback\FeedbackFieldAnswer;
use App\Entity\Sync\ClientUser;
use App\Enum\Shared\StatusEnum;
use App\Repository\ClientUserRepository;
use App\Repository\FeedbackFieldAnswerRepository;
use App\Repository\ServiceHistoryRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sync/clients')]
class ClientController extends AbstractController
{
    public function __construct(
        private readonly ClientUserRepository $clientUserRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly FeedbackFieldAnswerRepository $feedbackFieldAnswerRepository,
        private readonly ServiceHistoryRepository $serviceHistoryRepository,
        private readonly EntityManagerInterface $em,

    ) {
    }

    #[Route('/{id}/view', name: 'sync_client_view', methods: ['GET'])]
    public function view(ClientUser $clientUser)
    {
//        $clientServices = $this->serviceRepository->findBy(['clientUser' => $clientUser]);
        $clientSurveys = $this->feedbackFieldAnswerRepository->findBy(['responder' => $clientUser]);
        $clientHistory = $this->serviceHistoryRepository->findBy(['creator' => $clientUser], ['createdAt' => 'DESC']);

        return $this->render('sync/client_view.html.twig', [
            'clientUser' => $clientUser,
            'clientServices' => [],
            'clientSurveys' => $clientSurveys,
            'clientHistory' => $clientHistory,
            'statuses' => StatusEnum::getChoices(),
        ]);
    }

    #[Route('/{clientId}/survey/{surveyId}/answers', name: 'client_survey_answers')]
    public function surveyAnswers(int $clientId, int $surveyId): Response
    {
        $answers = [];
        // загрузка вопросов и ответов
        return $this->render('sync/_survey_answers.html.twig', [
            'answers' => $answers,
        ]);
    }

    #[Route('/index', name: 'sync_client_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $qb = $this->clientUserRepository->createQueryBuilder('c');

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('sync/client_index.html.twig', [
            'pagination' => $pagination,
            'statuses'      => StatusEnum::getChoices(),
        ]);
    }


}