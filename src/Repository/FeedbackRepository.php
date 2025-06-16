<?php

namespace App\Repository;

use App\DTO\Feedback\FeedbackFilterDto;
use App\DTO\Feedback\FeedbackSortDto;
use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackFieldAnswer;
use App\Entity\Feedback\FeedbackManager;
use App\Enum\UserRoleEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Feedback>
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function getFeedbackWithAnswersByClient(int $clientId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('f.id AS feedback_id', 'f.name AS feedback_name', 'ffa.createdAt AS answered_at', 'ff.label AS question_label', 'ffa.value AS answer_value')
            ->from(FeedbackFieldAnswer::class, 'ffa')
            ->join('ffa.field', 'ff')
            ->join('ff.feedback', 'f')
            ->where('ffa.responder = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('f.createdAt', 'DESC')
            ->addOrderBy('ff.sortOrder', 'ASC');

        $rows = $qb->getQuery()->getArrayResult();

        $grouped = [];
        foreach ($rows as $row) {
            $id = $row['feedback_id'];
            if (!isset($grouped[$id])) {
                $grouped[$id] = [
                    'name' => $row['feedback_name'],
                    'created_at' => $row['answered_at'],
                    'answers' => [],
                ];
            }
            $grouped[$id]['answers'][] = [
                'label' => $row['question_label'],
                'value' => $row['answer_value'],
            ];
        }

        return array_values($grouped);
    }

    public function getCountByDayOfWeekChartData(Feedback $feedback): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
SELECT EXTRACT(DOW FROM feedback_field_answer.created_at) AS dow, COUNT(DISTINCT feedback.id) AS cnt
FROM feedback_field_answer
INNER JOIN feedback_field ON feedback_field.id = feedback_field_answer.field_id
INNER JOIN feedback ON feedback.id = feedback_field.feedback_id
WHERE feedback_field.feedback_id = :feedbackId
GROUP BY dow
ORDER BY dow
    ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['feedbackId' => $feedback->getId()]);

        $rows = $result->fetchAllAssociative();

        // Инициализируем массив с нулями для каждого дня (0-6)
        $countsByDay = array_fill(0, 7, 0);

        $countsByDay = array_fill(0, 7, 0);

        foreach ($rows as $row) {
            $countsByDay[(int)$row['dow']] = (int)$row['cnt'];
        }

        $orderedCounts = [
            $countsByDay[1], // Пн
            $countsByDay[2], // Вт
            $countsByDay[3], // Ср
            $countsByDay[4], // Чт
            $countsByDay[5], // Пт
            $countsByDay[6], // Сб
            $countsByDay[0], // Вс
        ];

        return [
            'question' => 'Количество отзывов по дням недели',
            'labels' => ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            'data' => $orderedCounts,
            'type' => 'line',
        ];
    }

    public function getFilteredQueryBuilder(FeedbackFilterDto $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f');

        if (!empty($filters->id)) {
            $qb->andWhere('f.id = :id')->setParameter('id', $filters->id);
        }

        if (!empty($filters->name)) {
            $qb->andWhere('f.name LIKE :name')->setParameter('name', '%' . $filters->name . '%');
        }

        if (!empty($filters->type)) {
            $qb->andWhere('f.type = :type')->setParameter('type', $filters->type);
        }

        if (!empty($filters->scope)) {
            $qb->andWhere('f.scope = :scope')->setParameter('scope', $filters->scope);
        }

        if (!empty($filters->status)) {
            $qb->andWhere('f.status = :status')->setParameter('status', $filters->status);
        }

        if ($filters->createdFrom) {
            $qb->andWhere('f.createdAt >= :createdFrom')
                ->setParameter('createdFrom', $filters->createdFrom->format('Y-m-d 00:00:00'));
        }

        if ($filters->createdTo) {
            $qb->andWhere('f.createdAt <= :createdTo')
                ->setParameter('createdTo', $filters->createdTo->format('Y-m-d 23:59:59'));
        }

        if ($filters->updatedFrom) {
            $qb->andWhere('f.updatedAt >= :updatedFrom')
                ->setParameter('updatedFrom', $filters->updatedFrom->format('Y-m-d 00:00:00'));
        }

        if ($filters->updatedTo) {
            $qb->andWhere('f.updatedAt <= :updatedTo')
                ->setParameter('updatedTo', $filters->updatedTo->format('Y-m-d 23:59:59'));
        }

        return $qb;
    }

    public function applySorting(QueryBuilder $qb, FeedbackSortDto $sort): QueryBuilder
    {
        $qb->orderBy($sort->field, $sort->direction);
        return $qb;
    }

    public function applyAccessCondition(QueryBuilder $qb, ?UserInterface $user): QueryBuilder
    {
        $isAdmin = $user !== null && in_array(UserRoleEnum::ADMIN->value, $user->getRoles(), true);

        if (!$isAdmin) {
            $qb
                ->join('f.feedbackEditors', 'fm')
                ->andWhere('fm.isActive = :isActive')
                ->andWhere('fm.editor = :currentUser')
                ->setParameter('isActive', FeedbackManager::STATUS_ACTIVE)
                ->setParameter('currentUser', $user);
        }

        return $qb;
    }
}
