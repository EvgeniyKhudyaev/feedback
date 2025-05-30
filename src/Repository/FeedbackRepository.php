<?php

namespace App\Repository;

use App\DTO\Feedback\FeedbackFilterDto;
use App\DTO\Feedback\FeedbackSortDto;
use App\Entity\Feedback\Feedback;
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


    public function countUniqueClientsForFeedback(int $feedbackId): int
    {
        $entityManager = $this->getEntityManager();

        $dql = "
        SELECT COUNT(DISTINCT answer.responder)
        FROM App\Entity\Feedback\FeedbackFieldAnswer answer
        JOIN answer.field field
        JOIN field.feedback feedback
        WHERE feedback.id = :feedbackId
    ";

        $query = $entityManager->createQuery($dql)
            ->setParameter('feedbackId', $feedbackId);

        return (int) $query->getSingleScalarResult();
    }

    //    /**
    //     * @return Feedback[] Returns an array of Feedback objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Feedback
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
