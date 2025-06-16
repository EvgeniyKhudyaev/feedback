<?php

namespace App\Entity\Feedback;

use App\Entity\Sync\ClientUser;
use App\Enum\Shared\StatusEnum;
use App\Repository\FeedbackFieldAnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Guid\Guid;

#[ORM\Entity(repositoryClass: FeedbackFieldAnswerRepository::class)]
class FeedbackFieldAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true, nullable: false)]
    private ?string $uuid = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFieldValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedbackField $field = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'feedbackFieldValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientUser $responder = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->uuid = Guid::uuid4()->toString();
    }

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

    public function getField(): ?FeedbackField
    {
        return $this->field;
    }

    public function setField(?FeedbackField $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getResponder(): ?ClientUser
    {
        return $this->responder;
    }

    public function setResponder(?ClientUser $responder): static
    {
        $this->responder = $responder;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
