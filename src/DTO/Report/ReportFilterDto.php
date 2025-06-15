<?php

namespace App\DTO\Report;

class ReportFilterDto
{
    public ?string $clientName = null;
    public ?\DateTimeInterface $createdFrom = null;
    public ?\DateTimeInterface $createdTo = null;
    public array $codeFilters = [];
    // другие фильтры
    public function __construct(array $data)
    {
        $this->clientName = $data['filter_name'] ?? null;
        $this->createdFrom = !empty($data['filter_created_from']) ? new \DateTime($data['filter_created_from']) : null;
        $this->createdTo = !empty($data['filter_created_to']) ? new \DateTime($data['filter_created_to']) : null;

        foreach ($data as $key => $value) {
            if (strpos($key, 'filter_code_') === 0) {
                $code = substr($key, strlen('filter_code_'));
                $this->codeFilters[$code] = $value;
            }
        }
    }
}

