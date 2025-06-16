<?php

namespace App\Repository;

use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackTarget;
use App\Entity\Sync\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function findServices(int $clientUserId): array
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('s', 'sh')
            ->from(Service::class, 's')
            ->join('s.serviceHistories', 'sh')
            ->where('sh.creator = :clientUser')
            ->setParameter('clientUser', $clientUserId)
            ->orderBy('s.id', 'ASC')
            ->addOrderBy('sh.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findTargetServicesByFeedback(Feedback $feedback): array
    {
        /** @var FeedbackTarget $target */
        $targetIds = array_map(
            fn($target) => (int)$target->getTarget(),
            $feedback->getActiveTargets()->toArray()
        );

        return $this->findBy(['id' => $targetIds]);
    }
}
