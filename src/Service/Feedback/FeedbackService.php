<?php

namespace App\Service\Feedback;

use App\DTO\Feedback\FeedbackFilterDto;
use App\DTO\Feedback\FeedbackSortDto;
use App\Repository\FeedbackRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class FeedbackService
{
    public function __construct(
        private FeedbackRepository $feedbackRepository,
        private PaginatorInterface $paginator,
        private Security           $security,
    ) {}

    public function getFilteredPagination(FeedbackFilterDto $filters, FeedbackSortDto $sort, int $page = 1, int $limit = 20): PaginationInterface
    {
        $qb = $this->feedbackRepository->getFilteredQueryBuilder($filters);
        $qb = $this->feedbackRepository->applySorting($qb, $sort);
        $qb = $this->feedbackRepository->applyAccessCondition($qb, $this->security->getUser());

        return $this->paginator->paginate($qb, $page, $limit);
    }
}