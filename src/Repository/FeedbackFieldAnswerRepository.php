<?php

namespace App\Repository;

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
            ->select('r.id AS responder, f.label AS field_label, a.value, a.createdAt')
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
            $field = $row['field_label'];
            $value = $row['value'];
            $createdAt = $row['createdAt'];

            $rows[$responder][$field] = [$value, $createdAt];
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
}