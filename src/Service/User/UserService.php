<?php

namespace App\Service\User;

use App\Entity\User\User;
use App\Enum\Shared\Status;
use App\Repository\UserRepository;
use DomainException;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }

    public function markAsDeleted(User $user): void
    {
        if ($user->getStatus() === Status::DELETED) {
            throw new DomainException("Пользователь {$user->getId()} уже удален.");
        }

        $user->setStatus(Status::DELETED);
        $this->userRepository->save($user);
    }
}