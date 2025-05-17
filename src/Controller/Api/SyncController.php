<?php

namespace App\Controller\Api;

use App\DTO\Sync\SyncPayloadDTO;
use App\Service\Sync\SyncService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SyncController extends AbstractController
{
    private SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    #[Route('/api/sync', name: 'sync', methods: ['POST'])]
    #[IsGranted('ROLE_API')]
    public function sync(
        #[MapRequestPayload] SyncPayloadDTO $dto
    ): JsonResponse
    {
        try {
            $this->syncService->process($dto);

            return new JsonResponse(['status' => 'success']);
        } catch (JsonException $exception) {
            return new JsonResponse(['error' => 'Некорректный JSON: ' . $exception->getMessage()], 400);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 500);
        }
    }
}