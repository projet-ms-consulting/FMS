<?php

namespace App\Controller\Dashboard;

use App\Entity\InvoiceMission;
use App\Entity\Mission;
use App\Form\InvoiceMissionType;
use App\Form\MissionType;
use App\Repository\InvoiceRepository;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/mission', name: 'dashboard_mission_')]
class MissionController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(MissionRepository $missionRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $missions = $missionRepository->paginateMissions($page, $limit);
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
            $mission->setUpdatedAt(new \DateTimeImmutable());
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
        if ($this->isCsrfTokenValid('delete' . $mission->getId(), $request->request->get('_token'))) {
            $entityManager->remove($mission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_mission_index');
    }

    #[Route('/{id}/invoice', name: 'invoice', methods: ['GET'])]
    public function invoice(Mission $mission, InvoiceRepository $invoiceRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $invoices = $invoiceRepository->paginateinvoices($page, $limit, $mission);
        return $this->render('dashboard/mission/invoice.html.twig', [
            'invoices' => $invoices,
            'mission' => $mission,
        ]);
    }

    #[Route('/{id}/invoice/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function invoiceNew(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        $invoice = new InvoiceMission();
        $invoiceForm = $this->createForm(InvoiceMissionType::class, $invoice);
        $invoiceForm->handleRequest($request);

        if ($invoiceForm->isSubmitted() && $invoiceForm->isValid()) {
            $file = $request->files->get('invoice_mission')['file'];
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId(), $fileName);
            $invoice->setRealFilename($file->getClientOriginalName());
            if (!$invoice->isPaid()) {
                $invoice->setPaymentDate(null);
            }
            $invoice->setFile($fileName);
            $invoice->setMission($mission);
            $invoice->setCreatedAt(new \DateTimeImmutable());
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

    #[Route('/{id}/invoice/edit', name: 'invoice_edit', methods: ['GET', 'POST'])]
    public function invoiceEdit(InvoiceMission $invoice, Request $request, EntityManagerInterface $em)
    {
        $mission = $invoice->getMission();
        $form = $this->createForm(InvoiceMissionType::class, $invoice, [
            'page' => 'edit',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // ajout du nouveau fichier
            if ($request->files->get('invoice_mission')['file']) {
                $oldFile = $invoice->getFile();
                // Suppression de l'ancien fichier
                if (file_exists($this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/' . $oldFile)) {
                    unlink($this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/' . $oldFile);
                }
                // Ajout du nouveau fichier
                $file = $request->files->get('invoice_mission')['file'];
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId(), $fileName);
                $invoice->setRealFilename($file->getClientOriginalName());
                $invoice->setFile($fileName);
            }
            $invoice->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return $this->redirectToRoute('dashboard_mission_invoice', ['id' => $mission->getId()]);
        }

        return $this->render('dashboard/mission/invoice_edit.html.twig', [
            'invoice' => $invoice,
            'mission' => $mission,
            'invoiceForm' => $form->createView(),
        ]);
    }


    #[Route('/{id}/invoice/{invoiceId}', name: 'invoice_show', methods: ['GET'])]
    public function invoiceShow(Mission $mission, $invoiceId, InvoiceRepository $invoiceRepository): Response
    {
        $invoice = $invoiceRepository->find($invoiceId);
        $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/' . $invoice->getFile();
        return $this->render('dashboard/mission/invoice_show.html.twig', [
            'mission' => $mission,
            'invoice' => $invoice,
            'file' => $file,
        ]);
    }

    #[Route('/{id}/invoice/{invoiceId}/{name}', name: 'invoice_show_invoice', methods: ['GET'])]
    public function invoiceShowFile(Mission $mission, $invoiceId, InvoiceRepository $invoiceRepository): Response
    {
        $invoice = $invoiceRepository->find($invoiceId);
        $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/' . $invoice->getFile();
        return $this->file($file, $invoice->getFile(), ResponseHeaderBag::DISPOSITION_INLINE);
    }

}