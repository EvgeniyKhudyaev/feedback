<?php

namespace App\Controller\Sync;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/sync/service-types')]
class ServiceTypeController
{
    #[Route('/index', name: 'sync_service_type_index', methods: ['GET'])]
    public function index()
    {

    }

    #[Route('/{serviceState}/view', name: 'sync_service_type_view', methods: ['GET'])]
    public function view(int $id)
    {

    }
}