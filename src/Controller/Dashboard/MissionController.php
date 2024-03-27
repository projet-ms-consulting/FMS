<?php

namespace App\Controller\Dashboard;

use App\Entity\Invoice;
use App\Entity\Mission;
use App\Form\InvoiceType;
use App\Form\MissionType;
use App\Repository\InvoiceRepository;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/mission', name: 'dashboard_mission_')]
class MissionController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(MissionRepository $missionRepository): Response
    {
        $missions = $missionRepository->findAll();
        return $this->render('dashboard/mission/index.html.twig', [
            'missions' => $missions,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mission = new Mission();
        $missionForm = $this->createForm(MissionType::class, $mission);
        $missionForm->handleRequest($request);

        if ($missionForm->isSubmitted() && $missionForm->isValid()) {
            $mission->setCreatedAt(new \DateTimeImmutable());
            $mission->setFinished(false);
            $entityManager->persist($mission);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_mission_index');
        }

        return $this->render('dashboard/mission/new.html.twig', [
            'mission' => $mission,
            'missionForm' => $missionForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Mission $mission): Response
    {
        return $this->render('dashboard/mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        $missionForm = $this->createForm(MissionType::class, $mission);
        $missionForm->handleRequest($request);

        if ($missionForm->isSubmitted() && $missionForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_mission_index');
        }

        return $this->render('dashboard/mission/edit.html.twig', [
            'mission' => $mission,
            'missionForm' => $missionForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mission->getId(), $request->request->get('_token'))) {
            $entityManager->remove($mission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_mission_index');
    }

    #[Route('/{id}/invoice', name: 'invoice', methods: ['GET'])]
    public function invoice(Mission $mission, InvoiceRepository $invoiceRepository): Response
    {
        $invoices = $invoiceRepository->findAll();

        return $this->render('dashboard/mission/invoice.html.twig', [
            'invoices' => $invoices,
            'mission' => $mission,
        ]);
    }

    #[Route('/{id}/invoice/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function invoiceNew(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice();
        $invoiceForm = $this->createForm(InvoiceType::class, $invoice);
        $invoiceForm->handleRequest($request);

        if ($invoiceForm->isSubmitted() && $invoiceForm->isValid()) {
            $invoice->setFile($request->files->get('invoice')['file']->getClientOriginalName());
            $invoice->setMission($mission);
            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_mission_invoice', ['id' => $mission->getId()]);
        }

        return $this->render('dashboard/mission/invoice_new.html.twig', [
            'mission' => $mission,
            'invoice' => $invoice,
            'invoiceForm' => $invoiceForm->createView(),
        ]);
    }

}