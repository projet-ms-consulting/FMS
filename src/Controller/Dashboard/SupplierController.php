<?php

namespace App\Controller\Dashboard;

use App\Entity\SupplierMission;
use App\Form\SupplierMissionType;
use App\Repository\SupplierMissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/supplier', name: 'dashboard_supplier_')]
class SupplierController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(SupplierMissionRepository $supplierMissionRepository): Response
    {
        $supplierMission = $supplierMissionRepository->findAll();
        return $this->render('dashboard/supplier/index.html.twig', [
            'supplierMissions' => $supplierMission,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $supplierMission = new SupplierMission();
        $supplierMissionForm = $this->createForm(SupplierMissionType::class, $supplierMission);
        $supplierMissionForm->handleRequest($request);

        if ($supplierMissionForm->isSubmitted() && $supplierMissionForm->isValid()) {
            $supplierMission->setCreatedAt(new \DateTimeImmutable());
            $supplierMission->setFinished(false);
            $entityManager->persist($supplierMission);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_supplier_index');
        }

        return $this->render('dashboard/supplier/new.html.twig', [
            'supplierMission' => $supplierMission,
            'supplierMissionForm' => $supplierMissionForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(SupplierMission $supplierMission): Response
    {
        return $this->render('dashboard/supplier/show.html.twig', [
            'supplierMission' => $supplierMission,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SupplierMission $supplierMission, EntityManagerInterface $entityManager): Response
    {
        $supplierMissionForm = $this->createForm(SupplierMissionType::class, $supplierMission);
        $supplierMissionForm->handleRequest($request);

        if ($supplierMissionForm->isSubmitted() && $supplierMissionForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_supplier_index');
        }

        return $this->render('dashboard/supplier/edit.html.twig', [
            'supplierMission' => $supplierMission,
            'supplierMissionForm' => $supplierMissionForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, SupplierMission $supplierMission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $supplierMission->getId(), $request->request->get('_token'))) {
            $entityManager->remove($supplierMission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_supplier_index');
    }

}