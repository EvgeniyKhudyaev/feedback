<?php

namespace App\Entity;

use App\Entity\Feedback\Feedback;
use App\Entity\Sync\ClientUser;
use App\Enum\Message\MessageStatusEnum;
use App\Enum\Message\MessageTypeEnum;
use App\Repository\MessageLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageLogRepository::class)]
class MessageLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'messageLogs')]
    private ?ClientUser $clientUser = null;

    #[ORM\ManyToOne(inversedBy: 'messageLogs')]
    private ?Feedback $feedback = null;

    #[ORM\Column(type: 'string', length: 50, enumType: MessageTypeEnum::class)]
    private ?MessageTypeEnum $type = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(type: 'string', length: 50, enumType: MessageStatusEnum::class)]
    private ?MessageStatusEnum $status = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $error = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $sentAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getClientUser(): ?ClientUser
    {
        return $this->clientUser;
    }

    public function setClientUser(?ClientUser $clientUser): static
    {
        $this->clientUser = $clientUser;

        return $this;
    }

    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback): static
    {
        $this->feedback = $feedback;

        return $this;
    }

    public function getType(): ?MessageTypeEnum
    {
        return $this->type;
    }

    public function setType(MessageTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?MessageStatusEnum
    {
        return $this->status;
    }

    public function setStatus(MessageStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): static
    {
        $this->error = $error;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }
}
