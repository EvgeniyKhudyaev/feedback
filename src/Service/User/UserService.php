<?php

namespace App\Service\User;

use App\Entity\User\User;
use App\Enum\Shared\StatusEnum;
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
        if ($user->getStatus() === StatusEnum::DELETED) {
            throw new DomainException("Пользователь {$user->getId()} уже удален.");
        }

        $user->setStatus(StatusEnum::DELETED);
        $this->userRepository->save($user);
    }
}