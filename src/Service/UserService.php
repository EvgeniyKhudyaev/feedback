<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\Status;
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
        if ($user->getStatus() === Status::Deleted) {
            throw new DomainException("Пользователь {$user->getId()} уже удален.");
        }

        $user->setStatus(Status::Deleted);
        $this->userRepository->save($user);
    }
}