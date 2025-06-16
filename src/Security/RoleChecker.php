<?php

namespace App\Security;


use Symfony\Bundle\SecurityBundle\Security;

class RoleChecker
{
    public function __construct(private Security $security) {}

    public function isAdminOrManager(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_MANAGER');
    }
}