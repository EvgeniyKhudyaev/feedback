<?php

namespace App\Service\Feedback;

use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackManager;
use App\Repository\FeedbackManagerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class FeedbackEditorManager
{
    public function __construct(
        private EntityManagerInterface    $em,
        private UserRepository            $userRepository,
        private FeedbackManagerRepository $feedbackManagerRepository,
    )
    {
    }

    public function updateEditors(Feedback $feedback, array $selectedUserIds): void
    {
        $currentEditorIds = $this->getCurrentEditorIds($feedback);
        $selectedUserIds = array_map('intval', $selectedUserIds);

        $toRemoveIds = array_diff($currentEditorIds, $selectedUserIds);
        $toAddIds = array_diff($selectedUserIds, $currentEditorIds);

        $this->removeEditors($feedback, $toRemoveIds);
        $this->addEditors($feedback, $toAddIds);
    }

    private function getCurrentEditorIds(Feedback $feedback): array
    {
        return array_map(
            fn($editor) => (int)$editor->getEditor()->getId(),
            $feedback->getActiveFeedbackEditors()->toArray()
        );
    }

    private function removeEditors(Feedback $feedback, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $feedbackManager = $this->feedbackManagerRepository->findOneBy([
                'editor' => $userId,
                'feedback' => $feedback
            ]);

            if ($feedbackManager === null) {
                throw new \RuntimeException("Менеджер обратной связи для редактора с ID {$userId} не найден.");
            }

            /** @var FeedbackManager $feedbackManager */
            $feedbackManager->setIsActive(FeedbackManager::STATUS_INACTIVE);
        }
    }

    private function addEditors(Feedback $feedback, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $user = $this->userRepository->find($userId);

            if ($user === null) {
                throw new \RuntimeException("Пользователь с ID {$userId} не найден.");
            }

            $feedbackManager = $this->feedbackManagerRepository->findOneBy([
                'editor' => $userId,
                'feedback' => $feedback,
            ]);


            if ($feedbackManager !== null) {
                if ($feedbackManager->getIsActive() === FeedbackManager::STATUS_ACTIVE) {
                    throw new \RuntimeException("Менеджер обратной связи для редактора с ID {$userId} уже существует.");
                }

                $feedbackManager->setIsActive(FeedbackManager::STATUS_ACTIVE);
                continue;
            }

            $feedbackManager = FeedbackManager::create($user, $feedback);
            $this->em->persist($feedbackManager);
            $feedback->addFeedbackEditor($feedbackManager);

        }
    }
}