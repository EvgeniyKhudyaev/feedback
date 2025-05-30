<?php

namespace App\Repository;

use App\DTO\User\UserFilterDto;
use App\DTO\User\UserSortDto;
use App\Entity\User;
use App\Enum\UserRoleEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getFilteredQueryBuilder(UserFilterDto $filters): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');

        if (!empty($filters->id)) {
            $qb->andWhere('u.id = :id')->setParameter('id', $filters->id);
        }

        if (!empty($filters->name)) {
            $qb->andWhere('u.name LIKE :name')->setParameter('name', '%' . $filters->name . '%');
        }

        if (!empty($filters->email)) {
            $qb->andWhere('u.type = :email')->setParameter('email', $filters->email);
        }

        if (!empty($filters->phone)) {
            $qb->andWhere('u.scope = :phone')->setParameter('phone', $filters->phone);
        }

        if (!empty($filters->telegram)) {
            $qb->andWhere('u.scope = :telegram')->setParameter('telegram', $filters->telegram);
        }

        if (!empty($filters->role)) {
            $qb->andWhere('u.scope = :role')->setParameter('role', $filters->role);
        }

        if (!empty($filters->status)) {
            $qb->andWhere('u.status = :status')->setParameter('status', $filters->status);
        }

        if ($filters->createdFrom) {
            $qb->andWhere('u.createdAt >= :createdFrom')
                ->setParameter('createdFrom', $filters->createdFrom->format('Y-m-d 00:00:00'));
        }

        if ($filters->createdTo) {
            $qb->andWhere('u.createdAt <= :createdTo')
                ->setParameter('createdTo', $filters->createdTo->format('Y-m-d 23:59:59'));
        }

        if ($filters->updatedFrom) {
            $qb->andWhere('u.updatedAt >= :updatedFrom')
                ->setParameter('updatedFrom', $filters->updatedFrom->format('Y-m-d 00:00:00'));
        }

        if ($filters->updatedTo) {
            $qb->andWhere('u.updatedAt <= :updatedTo')
                ->setParameter('updatedTo', $filters->updatedTo->format('Y-m-d 23:59:59'));
        }

        return $qb;
    }

    public function applySorting(QueryBuilder $qb, UserSortDto $sort): QueryBuilder
    {
        $qb->orderBy($sort->field, $sort->direction);
        return $qb;
    }

        //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findByRole(UserRoleEnum $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role = :role')
            ->setParameter('role', $role->value)
            ->getQuery()
            ->getResult();
    }
}
