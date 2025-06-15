<?php

namespace App\Repository;

use App\DTO\Report\ReportFilterDto;
use App\DTO\Report\ReportSortDto;
use App\Entity\Feedback\FeedbackFieldAnswer;
use App\Entity\Feedback\FeedbackFieldOption;
use App\Enum\Feedback\FeedbackFieldTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FeedbackFieldAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedbackFieldAnswer::class);
    }

    public function findAnswersByFeedback(int $feedbackId, ReportFilterDto $filters): array
    {
        $em = $this->getEntityManager();

        // 1. Найдём id респондентов, у которых есть совпадения по фильтрам code => value
        $responderIds = null;
        if (!empty($filters->codeFilters)) {
            $subQb = $em->createQueryBuilder()
                ->select('DISTINCT r.id')
                ->from(FeedbackFieldAnswer::class, 'a')
                ->join('a.responder', 'r')
                ->join('a.field', 'f')
                ->join('f.feedback', 'fb')
                ->where('fb.id = :feedbackId')
                ->setParameter('feedbackId', $feedbackId);

            foreach ($filters->codeFilters as $code => $value) {
                if ($value === '' || $value === null) {
                    continue;
                }

                $subQb->andWhere("f.code = :code AND a.value LIKE :value");
                $subQb->setParameter("code", $code);
                $subQb->setParameter("value", '%' . $value . '%');
            }

            $responderIds = array_column($subQb->getQuery()->getArrayResult(), 'id');

            if (empty($responderIds)) {
                return []; // Никто не подошёл — сразу возвращаем пусто
            }
        }

        // 2. Основной запрос: все ответы по нужным условиям
        $qb = $this->createQueryBuilder('a')
            ->select('r.name AS responder, r.id AS clientId, f.label AS field_label, a.value, f.type, a.createdAt')
            ->join('a.responder', 'r')
            ->join('a.field', 'f')
            ->join('f.feedback', 'fb')
            ->where('fb.id = :feedbackId')
            ->setParameter('feedbackId', $feedbackId)
            ->orderBy('r.id');

        if ($filters->createdFrom) {
            $qb->andWhere('a.createdAt >= :createdFrom')
                ->setParameter('createdFrom', $filters->createdFrom->format('Y-m-d 00:00:00'));
        }

        if ($filters->createdTo) {
            $qb->andWhere('a.createdAt <= :createdTo')
                ->setParameter('createdTo', $filters->createdTo->format('Y-m-d 23:59:59'));
        }

        if (!empty($filters->clientName)) {
            $qb->andWhere('r.name LIKE :clientName')->setParameter('clientName', '%' . $filters->clientName . '%');
        }

        if ($responderIds !== null) {
            $qb->andWhere('r.id IN (:responderIds)')
                ->setParameter('responderIds', $responderIds);
        }


        $results = $qb->getQuery()->getArrayResult();


        // Преобразуем в табличный вид
        $rows = [];
        foreach ($results as $row) {
            $responder = $row['responder'];
            $clientId = $row['clientId'];
            $field = $row['field_label'];
            $type = $row['type'];
            $value = $row['value'];
            $createdAt = $row['createdAt'];

//            if (in_array($type, [FeedbackFieldTypeEnum::SELECT, FeedbackFieldTypeEnum::RADIO, FeedbackFieldTypeEnum::MULTISELECT])) {
//                $ids = explode(',', $value);
//                $ids = array_map('intval', $ids);
//
//                $qb = $em->createQueryBuilder();
//
//                $options = $qb->select('o.value')
//                    ->from(FeedbackFieldOption::class, 'o')
//                    ->where($qb->expr()->in('o.id', ':ids'))
//                    ->setParameter('ids', $ids) // не нужно указывать тип, DQL сам разберется
//                    ->getQuery()
//                    ->getResult();
//
//                $value = implode(',', array_column($options, 'value'));
//            } elseif ($type === FeedbackFieldTypeEnum::CHECKBOX) {
//                $value = (bool)$value ? 'Да' : 'Нет';
//            }


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

        if ($filters->createdFrom) {
            $qb->andWhere('a.createdAt >= :dateFrom')
                ->setParameter('dateFrom', $filters->createdFrom->format('Y-m-d 00:00:00'));
        }
        if ($filters->createdTo) {
            $qb->andWhere('a.createdAt <= :dateTo')
                ->setParameter('dateTo', $filters->createdTo->format('Y-m-d 23:59:59'));
        }

        $allowedSortFields = ['createdAt', 'clientName']; // список разрешённых для сортировки полей
        $sortField = in_array($sort->field, $allowedSortFields) ? 'a.' . $sort->field : 'a.createdAt';

        $qb->orderBy($sortField, $sort->direction === 'desc' ? 'DESC' : 'ASC');

        return $qb->getQuery()->getResult();
    }
}