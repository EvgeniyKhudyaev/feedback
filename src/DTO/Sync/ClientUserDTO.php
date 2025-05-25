<?php

namespace App\DTO\Sync;

use App\Enum\Shared\StatusEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ClientUserDTO
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $uuid;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    public string $email;

    #[Assert\Length(max: 20)]
    public ?string $phone = null;

    #[Assert\Length(max: 100)]
    public ?string $telegram = null;

    #[Assert\NotBlank]
    public StatusEnum $status;
}