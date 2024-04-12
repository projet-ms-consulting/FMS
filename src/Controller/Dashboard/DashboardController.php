<?php

namespace App\Controller\Dashboard;

use App\Repository\CompanyRepository;
use App\Repository\MissionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard', name: 'dashboard_')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, MissionRepository $missionRepository, CompanyRepository $companyRepository): Response
    {
        $userCount = $userRepository->count([]);
        $missionCount = $missionRepository->count([]);
        $companyCount = $companyRepository->count([]);

        return $this->render('dashboard/home/index.html.twig', [
            'userCount' => $userCount,
            'missionCount' => $missionCount,
            'companyCount' => $companyCount,
        ]);
    }
}
