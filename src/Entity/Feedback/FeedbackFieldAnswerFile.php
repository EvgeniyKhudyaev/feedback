<?php

namespace App\Entity\Feedback;

use App\Entity\File;
use App\Entity\Sync\ClientUser;
use App\Enum\Shared\StatusEnum;
use App\Repository\FeedbackFieldAnswerFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackFieldAnswerFileRepository::class)]
class FeedbackFieldAnswerFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?File $file = null;

    #[ORM\ManyToOne(targetEntity: FeedbackFieldAnswer::class,)]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedbackFieldAnswer $answer = null;

    #[ORM\ManyToOne(targetEntity: ClientUser::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientUser $creator = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, enumType: StatusEnum::class)]
    private ?StatusEnum $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getAnswer(): ?FeedbackFieldAnswer
    {
        return $this->answer;
    }

    public function setAnswer(?FeedbackFieldAnswer $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getCreator(): ?ClientUser
    {
        return $this->creator;
    }

    public function setCreator(?ClientUser $creator): static
    {
        $this->creator = $creator;

        return $this;
    }

    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }

    public function setStatus(StatusEnum $status): static
    {
        $this->status = $status;

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