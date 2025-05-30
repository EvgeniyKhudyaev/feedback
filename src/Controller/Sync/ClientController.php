<?php

namespace App\Controller\Sync;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/sync/clients')]
class ClientController
{
    #[Route('/index', name: 'sync_client_index', methods: ['GET'])]
    public function index()
    {

    }

    #[Route('/{client}/view', name: 'sync_client_view', methods: ['GET'])]
    public function view(int $id)
    {

    }
}