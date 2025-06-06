<?php

namespace App\Repository;

use App\DTO\Report\ReportFilterDto;
use App\DTO\Report\ReportSortDto;
use App\Entity\Feedback\FeedbackFieldAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FeedbackFieldAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbackFieldAnswer::class);
    }

    public function findAnswersByFeedback(int $feedbackId): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('r.name AS responder, r.id AS clientId, f.label AS field_label, a.value, a.createdAt')
            ->join('a.responder', 'r')           // присоединяем ClientUser
            ->join('a.field', 'f')               // присоединяем FeedbackField
            ->join('f.feedback', 'fb')           // присоединяем Feedback
            ->where('fb.id = :feedbackId')
            ->setParameter('feedbackId', $feedbackId)
            ->orderBy('r.id');

        $results = $qb->getQuery()->getArrayResult();

        // Преобразуем в табличный вид: responder => [field_label => value]
        $rows = [];

        foreach ($results as $row) {
            $responder = $row['responder'];
            $clientId = $row['clientId'];
            $field = $row['field_label'];
            $value = $row['value'];
            $createdAt = $row['createdAt'];

            $rows[$responder][$field] = [$value, $createdAt, $responder, $clientId];
        }

        return array_values($rows);
    }

    public function findAllGroupedByFeedback(): array
    {
        $answers = $this->createQueryBuilder('a')
            ->join('a.field', 'f')
            ->join('f.feedback', 'fb')
            ->addSelect('f', 'fb')
            ->getQuery()
            ->getResult();

        $grouped = [];

        foreach ($answers as $answer) {
            $feedbackId = $answer->getField()->getFeedback()->getId();
            $grouped[$feedbackId][] = $answer;
        }

        return $grouped;
    }

    public function getFilteredAnswers(int $feedbackId, ReportFilterDto $filters, ReportSortDto $sort)
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.feedback', 'f')
            ->where('f.id = :feedbackId')
            ->setParameter('feedbackId', $feedbackId);

        if ($filters->clientName) {
            $qb->andWhere('a.clientName LIKE :clientName')
                ->setParameter('clientName', '%'.$filters->clientName.'%');
        }

        if ($filters->dateFrom) {
            $qb->andWhere('a.createdAt >= :dateFrom')
                ->setParameter('dateFrom', $filters->dateFrom->format('Y-m-d 00:00:00'));
        }
        if ($filters->dateTo) {
            $qb->andWhere('a.createdAt <= :dateTo')
                ->setParameter('dateTo', $filters->dateTo->format('Y-m-d 23:59:59'));
        }

        $allowedSortFields = ['createdAt', 'clientName']; // список разрешённых для сортировки полей
        $sortField = in_array($sort->field, $allowedSortFields) ? 'a.' . $sort->field : 'a.createdAt';

        $qb->orderBy($sortField, $sort->direction === 'desc' ? 'DESC' : 'ASC');

        return $qb->getQuery()->getResult();
    }
}