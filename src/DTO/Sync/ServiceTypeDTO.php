<?php

namespace App\DTO\Sync;

use App\Enum\Shared\StatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceTypeDTO
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $uuid;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    public string $description;

    #[Assert\NotBlank]
    public StatusEnum $status;
}