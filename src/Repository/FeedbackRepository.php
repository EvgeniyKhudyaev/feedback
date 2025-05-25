<?php

namespace App\Repository;

use App\Entity\Feedback\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedback>
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function getFilteredQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f');

        if (!empty($filters['id'])) {
            $qb->andWhere('f.id = :id')->setParameter('id', $filters['id']);
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('f.name LIKE :name')->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('f.type = :type')->setParameter('type', $filters['type']);
        }

        if (!empty($filters['scope'])) {
            $qb->andWhere('f.scope = :scope')->setParameter('scope', $filters['scope']);
        }

        if (!empty($filters['status'])) {
            $qb->andWhere('f.status = :status')->setParameter('status', $filters['status']);
        }

        return $qb;
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
