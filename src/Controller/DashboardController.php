<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard_index')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }
 
    #[Route("/dashboard/users", name: 'dashboard_users')]
    public function users(): Response
    {
        return $this->render('dashboard/users.html.twig');
    }
}
