<?php

namespace App\Controller\Dashboard;

use App\Entity\InvoiceMission;
use App\Entity\Mission;
use App\Form\InvoiceMissionType;
use App\Form\MissionType;
use App\Repository\InvoiceRepository;
use App\Repository\InvoiceSupplierRepository;
use App\Repository\MissionRepository;
use App\Repository\SupplierMissionRepository;
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

            // suppression de toutes les factures liees
            foreach ($mission->getInvoices() as $invoice) {
                $this->deleteInvoiceSuppliers($invoice, $mission, $entityManager);
            }

            // suppression du dossier de la mission
            if (file_exists($this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId())) {
                $this->recursiveRemoveDirectory($this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId());
            }

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

    #[Route('/{id}/invoice/links', name: 'invoice_links', methods: ['GET'])]
    public function invoiceLinks(Mission $mission, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $totalHTFournisseur = 0;
        $totalTTCFournisseur = 0;
        $totalJoursFournisseur = 0;
        $totalHeureFournisseur = 0;

        $totalHTClient = 0;
        $totalTTCClient = 0;
        $totalJoursClient = 0;

        for ($i = 0; $i < count($mission->getSupplierMission()->getValues()); $i++) {
            for ($j = 0; $j < count($mission->getSupplierMission()->getValues()[$i]->getInvoices()->getValues()); $j++) {
                if ($mission->getSupplierMission()->getValues()[$i]->getInvoices()->getValues()[$j]->getUnit() == 'Jour') {
                    $totalJoursFournisseur += $mission->getSupplierMission()->getValues()[$i]->getInvoices()->getValues()[$j]->getQuantity();
                } elseif ($mission->getSupplierMission()->getValues()[$i]->getInvoices()->getValues()[$j]->getUnit() == 'Heure') {
                    $totalJoursFournisseur += ($mission->getSupplierMission()->getValues()[$i]->getInvoices()->getValues()[$j]->getQuantity() / 7);
                }

                $totalHTFournisseur += $mission->getSupplierMission()->getValues()[$i]->getInvoices()->getValues()[$j]->getTotalHT();
                $totalTTCFournisseur += $mission->getSupplierMission()->getValues()[$i]->getInvoices()->getValues()[$j]->getTotalTTC();
            }
        }
        for ($i = 0; $i < count($mission->getInvoices()->getValues()); $i++) {
            if ($mission->getInvoices()->getValues()[$i]->getUnit() == 'Jour') {
                $totalJoursClient += $mission->getInvoices()->getValues()[$i]->getQuantity();
            } elseif ($mission->getInvoices()->getValues()[$i]->getUnit() == 'Heure') {
                $totalJoursClient += ($mission->getInvoices()->getValues()[$i]->getQuantity() / 8);
            }

            $totalHTClient += $mission->getInvoices()->getValues()[$i]->getTotalHT();
            $totalTTCClient += $mission->getInvoices()->getValues()[$i]->getTotalTTC();
        }
        dump($totalHTFournisseur);
        dump($totalTTCFournisseur);
        dump($totalJoursFournisseur);
        dump($totalHTClient);
        dump($totalTTCClient);
        dump($totalJoursClient);
        return $this->render('dashboard/mission/invoice_links.html.twig', [
            'mission' => $mission,
            'totalHTFournisseur' => $totalHTFournisseur,
            'totalTTCFournisseur' => $totalTTCFournisseur,
            'totalJoursFournisseur' => $totalJoursFournisseur,
            'totalHTClient' => $totalHTClient,
            'totalTTCClient' => $totalTTCClient,
            'totalJoursClient' => $totalJoursClient,
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

    #[Route('/{id}/invoice/{invoiceId}/delete', name: 'invoice_delete', methods: ['POST'])]
    public function invoiceDelete(Request $request, Mission $mission, InvoiceMission $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $mission->getId(), $request->request->get('_token'))) {
            // suppression de toutes les factures liees
            $this->deleteInvoiceSuppliers($invoice, $mission, $entityManager);
            $entityManager->flush();
        }
        return $this->redirectToRoute('dashboard_mission_invoice', ['id' => $mission->getId()]);
    }

    private function recursiveRemoveDirectory($dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->recursiveRemoveDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * @param mixed $invoice
     * @param Mission $mission
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function deleteInvoiceSuppliers(mixed $invoice, Mission $mission, EntityManagerInterface $entityManager): void
    {
        foreach ($invoice->getInvoiceSuppliers()->getValues() as $invoiceSupplier) {
            $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/supplier/' . $invoiceSupplier->getFile();
            if (file_exists($file)) {
                unlink($file);
            }
            $entityManager->remove($invoiceSupplier);
        }

        // Delete invoice
        $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $mission->getId() . '/' . $invoice->getFile();
        if (file_exists($file)) {
            unlink($file);
        }
        $entityManager->remove($invoice);
    }

}
