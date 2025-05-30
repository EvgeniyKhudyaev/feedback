<?php

namespace App\DTO\User;

class UserFilterDto
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $telegram = null;
    public ?string $role = null;
    public ?string $status = null;
    public ?\DateTimeInterface $createdFrom = null;
    public ?\DateTimeInterface $createdTo = null;
    public ?\DateTimeInterface $updatedFrom = null;
    public ?\DateTimeInterface $updatedTo = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['filter_id']) && is_numeric($data['filter_id'])
            ? (int)$data['filter_id']
            : null;
        $this->name = $data['filter_name'] ?? null;
        $this->email = $data['filter_email'] ?? null;
        $this->phone = $data['filter_phone'] ?? null;
        $this->telegram = $data['filter_telegram'] ?? null;
        $this->role = $data['filter_role'] ?? null;
        $this->status = $data['filter_status'] ?? null;
        $this->createdFrom = !empty($data['filter_created_from']) ? new \DateTime($data['filter_created_from']) : null;
        $this->createdTo = !empty($data['filter_created_to']) ? new \DateTime($data['filter_created_to']) : null;
        $this->updatedFrom = !empty($data['filter_updated_from']) ? new \DateTime($data['filter_updated_from']) : null;
        $this->updatedTo = !empty($data['filter_updated_to']) ? new \DateTime($data['filter_updated_to']) : null;
    }
}