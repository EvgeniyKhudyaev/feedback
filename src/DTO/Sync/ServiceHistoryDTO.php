<?php

namespace App\DTO\Sync;

use App\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceHistoryDTO
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $uuid;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $serviceUuid;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $clientUserUuid;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $stateUuid;

    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    public string $note;

    #[Assert\NotBlank]
    public Status $status;
}