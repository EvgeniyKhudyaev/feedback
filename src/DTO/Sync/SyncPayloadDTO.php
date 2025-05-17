<?php

namespace App\DTO\Sync;

use Symfony\Component\Validator\Constraints as Assert;

class SyncPayloadDTO
{
    /**
     * @var ClientUserDTO[]
     */
    #[Assert\All([new Assert\Type(ClientUserDTO::class)])]
    #[Assert\Valid]
    public array $clientUsers = [];

    /**
     * @var ServiceStateDTO[]
     */
    #[Assert\All([new Assert\Type(ServiceStateDTO::class)])]
    #[Assert\Valid]
    public array $serviceStates = [];

    /**
     * @var ServiceTypeDTO[]
     */
    #[Assert\All([new Assert\Type(ServiceTypeDTO::class)])]
    #[Assert\Valid]
    public array $serviceTypes = [];

    /**
     * @var ServiceDTO[]
     */
    #[Assert\All([new Assert\Type(ServiceDTO::class)])]
    #[Assert\Valid]
    public array $services = [];

    /**
     * @var ServiceHistoryDTO[]
     */
    #[Assert\All([new Assert\Type(ServiceHistoryDTO::class)])]
    #[Assert\Valid]
    public array $serviceHistories = [];
}