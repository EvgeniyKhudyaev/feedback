<?php

namespace App\DTO\User;

class UserSortDto
{
    public string $field;
    public string $direction;
    private array $allowedFields = ['u.id', 'u.name', 'u.email', 'u.phone', 'u.telegram', 'u.role', 'u.status', 'f.createdAt', 'f.updatedAt'];

    public function __construct(array $data = [])
    {
        $this->field = $data['sort'] ?? 'f.id';
        $this->direction = strtoupper($data['direction'] ?? 'ASC');

        if (!in_array($this->direction, ['ASC', 'DESC'], true)) {
            $this->direction = 'ASC';
        }

        if (!in_array($this->field, $this->allowedFields, true)) {
            $this->field = 'id';
        }
    }
}