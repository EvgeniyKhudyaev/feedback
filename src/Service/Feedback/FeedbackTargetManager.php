<?php

namespace App\Service\Feedback;

use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackTarget;
use App\Enum\Feedback\FeedbackTargetTypeEnum;
use App\Repository\FeedbackTargetRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class FeedbackTargetManager
{
    public function __construct(
        private EntityManagerInterface   $em,
        private ServiceRepository $serviceRepository,
        private FeedbackTargetRepository $feedbackTargetRepository,
    )
    {
    }

    public function updateTargets(Feedback $feedback, array $selectedRelationsIds): void
    {
        $currentRelationIds = $this->getCurrentRelationsIds($feedback);
        $selectedRelationsIds = array_map('intval', $selectedRelationsIds);

        $toRemoveIds = array_diff($currentRelationIds, $selectedRelationsIds);
        $toAddIds = array_diff($selectedRelationsIds, $currentRelationIds);

        $this->removeTargets($feedback, $toRemoveIds);
        $this->addTargets($feedback, $toAddIds);
    }

    private function getCurrentRelationsIds(Feedback $feedback): array
    {
        /** @var FeedbackTarget $target */
        return array_map(
            fn($target) => (int)$target->getTarget(),
            $feedback->getActiveTargets()->toArray()
        );
    }

    private function removeTargets(Feedback $feedback, array $targetIds): void
    {
        foreach ($targetIds as $targetId) {
            $feedbackTarget = $this->feedbackTargetRepository->findOneBy([
                'target' => $targetId,
                'feedback' => $feedback,
                'targetType' => FeedbackTargetTypeEnum::SERVICE->value
            ]);

            if ($feedbackTarget === null) {
                throw new \RuntimeException("Связь {$feedbackTarget} не найден.");
            }

            $feedbackTarget->setIsActive(FeedbackTarget::STATUS_INACTIVE);
        }
    }

    private function addTargets(Feedback $feedback, array $serviceIds): void
    {
        foreach ($serviceIds as $serviceId) {
            $service = $this->serviceRepository->find($serviceId);

            if ($service === null) {
                throw new \RuntimeException("Сервис с ID {$serviceId} не найден.");
            }

            $feedbackTarget = $this->feedbackTargetRepository->findOneBy([
                'target' => $serviceId,
                'feedback' => $feedback,
                'targetType' => FeedbackTargetTypeEnum::SERVICE->value
            ]);


            if ($feedbackTarget !== null) {
                if ($feedbackTarget->getIsActive() === FeedbackTarget::STATUS_ACTIVE) {
                    throw new \RuntimeException("Сервис с ID {$serviceId} уже существует.");
                }

                $feedbackTarget->setIsActive(FeedbackTarget::STATUS_ACTIVE);
                continue;
            }

            $feedbackTarget = FeedbackTarget::create($service->getId(), $feedback);
            $this->em->persist($feedbackTarget);
            $feedback->addTarget($feedbackTarget);

        }
    }
}