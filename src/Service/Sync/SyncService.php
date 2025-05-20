<?php

namespace App\Service\Sync;

use App\DTO\Sync\ClientUserDTO;
use App\DTO\Sync\ServiceDTO;
use App\DTO\Sync\ServiceHistoryDTO;
use App\DTO\Sync\ServiceStateDTO;
use App\DTO\Sync\ServiceTypeDTO;
use App\DTO\Sync\SyncPayloadDTO;
use App\Entity\Sync\ClientUser;
use App\Entity\Sync\Service;
use App\Entity\Sync\ServiceHistory;
use App\Entity\Sync\ServiceState;
use App\Entity\Sync\ServiceType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class SyncService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function process(SyncPayloadDTO $dto): void
    {
        $this->em->beginTransaction();
        try {
            $this->syncClientUsers($dto->clientUsers);
            $this->syncServiceStates($dto->serviceStates);
            $this->syncServiceTypes($dto->serviceTypes);
            $this->syncServices($dto->services);
            $this->syncServiceHistories($dto->serviceHistories);

            $this->em->commit();
        } catch (Exception $exception) {
            $this->em->rollback();
            throw $exception;
        }

    }

    private function syncClientUsers(array $clientUserDTOs): void
    {
        $clientUseRepository = $this->em->getRepository(ClientUser::class);

        /** @var ClientUserDTO $clientUserDTO */
        foreach ($clientUserDTOs as $clientUserDTO) {
            $clientUser = $clientUseRepository->findOneBy(['uuid' => $clientUserDTO->uuid]);

            if ($clientUser === null) {
                $clientUser = new ClientUser();
                $clientUser->setUuid($clientUserDTO->uuid);
            }

            $clientUser->setName($clientUserDTO->name);
            $clientUser->setEmail($clientUserDTO->email);
            $clientUser->setPhone($clientUserDTO->phone);
            $clientUser->setTelegram($clientUserDTO->telegram);
            $clientUser->setStatus($clientUserDTO->status);

            $this->em->persist($clientUser);
        }

        $this->em->flush();
    }
    private function syncServiceStates(array $serviceStateDTOs): void
    {
        $serviceStateRepository = $this->em->getRepository(ServiceState::class);

        /** @var ServiceStateDTO $serviceStateDTO */
        foreach ($serviceStateDTOs as $serviceStateDTO) {
            $serviceState = $serviceStateRepository->findOneBy(['uuid' => $serviceStateDTO->uuid]);

            if ($serviceState === null) {
                $serviceState = new ServiceState();
                $serviceState->setUuid($serviceStateDTO->uuid);
            }

            $serviceState->setName($serviceStateDTO->name);
            $serviceState->setDescription($serviceStateDTO->description);
            $serviceState->setStatus($serviceStateDTO->status);

            $this->em->persist($serviceState);
        }

        $this->em->flush();
    }

    private function syncServiceTypes(array $serviceTypeDTOs): void
    {
        $serviceTypeRepository = $this->em->getRepository(ServiceType::class);

        /** @var ServiceTypeDTO $serviceTypeDTO */
        foreach ($serviceTypeDTOs as $serviceTypeDTO) {
            $serviceType = $serviceTypeRepository->findOneBy(['uuid' => $serviceTypeDTO->uuid]);

            if ($serviceType === null) {
                $serviceType = new ServiceType();
                $serviceType->setUuid($serviceTypeDTO->uuid);
            }

            $serviceType->setName($serviceTypeDTO->name);
            $serviceType->setDescription($serviceTypeDTO->description);
            $serviceType->setStatus($serviceTypeDTO->status);

            $this->em->persist($serviceType);
        }

        $this->em->flush();
    }

    private function syncServices(array $serviceDTOs): void
    {
        $serviceRepository = $this->em->getRepository(Service::class);

        /** @var ServiceDTO $serviceDTO */
        foreach ($serviceDTOs as $serviceDTO) {
            $service = $serviceRepository->findOneBy(['uuid' => $serviceDTO->uuid]);

            if ($service === null) {
                $service = new Service();
                $service->setUuid($serviceDTO->uuid);
            }

            $service->setName($serviceDTO->name);
            $service->setDescription($serviceDTO->description);

            $serviceTypeRepository = $this->em->getRepository(ServiceType::class);
            $serviceType = $serviceTypeRepository->findOneBy(['uuid' => $serviceDTO->serviceTypeUuid]);

            if ($serviceType === null) {
                throw new \DomainException("ServiceType {$serviceDTO->serviceTypeUuid} не найден.");
            }

            $service->setServiceType($serviceType);
            $service->setStatus($serviceDTO->status);

            $this->em->persist($service);
        }

        $this->em->flush();
    }

    private function syncServiceHistories(array $serviceHistoryDTOs): void
    {
        $serviceHistoryRepository = $this->em->getRepository(ServiceHistory::class);
        $serviceRepository = $this->em->getRepository(Service::class);
        $clientUserRepository = $this->em->getRepository(ClientUser::class);
        $serviceStateRepository = $this->em->getRepository(ServiceState::class);

        /** @var ServiceHistoryDTO $serviceHistoryDTO */
        foreach ($serviceHistoryDTOs as $serviceHistoryDTO) {
            $serviceHistory = $serviceHistoryRepository->findOneBy(['uuid' => $serviceHistoryDTO->uuid]);

            if ($serviceHistory === null) {
                $serviceHistory = new ServiceHistory();
                $serviceHistory->setUuid($serviceHistoryDTO->uuid);
            }

            $service = $serviceRepository->findOneBy(['uuid' => $serviceHistoryDTO->serviceUuid]);

            if ($service === null) {
                throw new \DomainException("Service {$serviceHistoryDTO->serviceUuid} не найден.");
            }

            $serviceHistory->setService($service);

            $clientUser = $clientUserRepository->findOneBy(['uuid' => $serviceHistoryDTO->clientUserUuid]);

            if ($clientUser === null) {
                throw new \DomainException("ClientUser {$serviceHistoryDTO->clientUserUuid} не найден.");
            }

            $serviceHistory->setCreator($clientUser);

            $serviceState = $serviceStateRepository->findOneBy(['uuid' => $serviceHistoryDTO->stateUuid]);

            if ($serviceState === null) {
                throw new \DomainException("ServiceState {$serviceHistoryDTO->stateUuid} не найден.");
            }

            $serviceHistory->setState($serviceState);
            $serviceHistory->setNote($serviceHistoryDTO->note);
            $serviceHistory->setStatus($serviceHistoryDTO->status);

            $this->em->persist($serviceHistory);
        }

        $this->em->flush();
    }
}
