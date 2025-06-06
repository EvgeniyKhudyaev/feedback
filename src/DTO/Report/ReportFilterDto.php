<?php

namespace App\DTO\Report;

class ReportFilterDto
{
    public ?string $clientName = null;
    public ?\DateTimeInterface $dateFrom = null;
    public ?\DateTimeInterface $dateTo = null;
    // другие фильтры
    public function __construct(array $data)
    {
        $this->clientName = $data['clientName'] ?? null;
        $this->dateFrom = !empty($data['dateFrom']) ? new \DateTime($data['dateFrom']) : null;
        $this->dateTo = !empty($data['dateTo']) ? new \DateTime($data['dateTo']) : null;
        // ...
    }
}

