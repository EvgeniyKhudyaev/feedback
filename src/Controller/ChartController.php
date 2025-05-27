<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/charts')]
class ChartController extends AbstractController
{
    #[Route('/example', name: 'chart_example')]
    public function index(): Response
    {
        return $this->render('chart/chart.html.twig');
    }
}