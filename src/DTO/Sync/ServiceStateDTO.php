<?php

namespace App\DTO\Sync;

use App\Enum\Shared\Status;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceStateDTO
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $uuid;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    public string $description;

    #[Assert\NotBlank]
    public Status $status;
}