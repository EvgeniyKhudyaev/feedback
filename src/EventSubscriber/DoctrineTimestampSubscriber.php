<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class DoctrineTimestampSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setCreatedAt') && method_exists($entity, 'setUpdatedAt')) {
            $now = new \DateTimeImmutable();
            $entity->setCreatedAt($now);
            $entity->setUpdatedAt($now);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
    }
}
