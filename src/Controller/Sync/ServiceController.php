<?php

namespace App\Controller\Sync;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/sync/services')]
class ServiceController
{
    #[Route('/index', name: 'sync_service_index', methods: ['GET'])]
    public function index()
    {

    }

    #[Route('/{service}/view', name: 'sync_service_view', methods: ['GET'])]
    public function view(int $id)
    {

    }
}