<?php

namespace App\Controller\Sync;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/sync/service-states')]
class ServiceStateController
{
    #[Route('/index', name: 'sync_service_state_index', methods: ['GET'])]
    public function index()
    {

    }

    #[Route('/{serviceState}/view', name: 'sync_service_state_view', methods: ['GET'])]
    public function view(int $id)
    {

    }
}