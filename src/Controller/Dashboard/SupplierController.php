<?php

namespace App\Controller\Dashboard;

use App\Entity\InvoiceSupplier;
use App\Entity\SupplierMission;
use App\Form\InvoiceSupplierType;
use App\Form\SupplierMissionType;
use App\Repository\InvoiceRepository;
use App\Repository\InvoiceSupplierRepository;
use App\Repository\SupplierMissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/supplier', name: 'dashboard_supplier_')]
class SupplierController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(SupplierMissionRepository $supplierMissionRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $supplierMission = $supplierMissionRepository->paginateSupplierMissions($page, $limit);
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
            $supplierMission->setUpdatedAt(new \DateTimeImmutable());
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

    #[Route('/{id}/invoice', name: 'invoice', methods: ['GET'])]
    public function invoice(SupplierMission $supplierMission, InvoiceSupplierRepository $invoiceSupplierRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $invoices = $invoiceSupplierRepository->paginateinvoices($page, $limit, $supplierMission);
        return $this->render('dashboard/supplier/invoice.html.twig', [
            'invoices' => $invoices,
            'supplierMission' => $supplierMission,
        ]);
    }

    #[Route('/{id}/invoice/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function invoiceNew(Request $request, SupplierMission $supplierMission, EntityManagerInterface $entityManager): Response
    {
        $invoice = new InvoiceSupplier();
        $invoiceForm = $this->createForm(InvoiceSupplierType::class, $invoice);
        $invoiceForm->handleRequest($request);

        if ($invoiceForm->isSubmitted() && $invoiceForm->isValid()) {
            $file = $request->files->get('invoice_supplier')['file'];
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/facture/mission/' . $supplierMission->getMission()->getId() . '/supplier', $fileName);
            $invoice->setRealFilename($file->getClientOriginalName());
            $invoice->setFile($fileName);
            $invoice->setSupplierMission($supplierMission);
            $invoice->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_supplier_invoice', ['id' => $supplierMission->getId()]);
        }

        return $this->render('dashboard/supplier/invoice_new.html.twig', [
            'supplierMission' => $supplierMission,
            'invoice' => $invoice,
            'invoiceForm' => $invoiceForm->createView(),
        ]);
    }

    #[Route('/{id}/invoice/edit', name: 'invoice_edit', methods: ['GET', 'POST'])]
    public function invoiceEdit(InvoiceSupplier $invoice, Request $request, EntityManagerInterface $em)
    {
        $mission = $invoice->getSupplierMission();
        $form = $this->createForm(InvoiceSupplierType::class, $invoice, [
            'page' => 'edit',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invoice->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return $this->redirectToRoute('dashboard_supplier_invoice', ['id' => $mission->getId()]);
        }

        return $this->render('dashboard/supplier/invoice_edit.html.twig', [
            'invoice' => $invoice,
            'mission' => $mission,
            'invoiceForm' => $form->createView(),
        ]);
    }


    #[Route('/{id}/invoice/{invoiceId}', name: 'invoice_show', methods: ['GET'])]
    public function invoiceShow(SupplierMission $mission, $invoiceId, InvoiceSupplierRepository $invoiceRepository): Response
    {
        $invoice = $invoiceRepository->find($invoiceId);
        $file = $this->getParameter('kernel.project_dir') . '/invoice/' . $mission->getId() . '/supplier/' . $invoice->getFile();
        return $this->render('dashboard/supplier/invoice_show.html.twig', [
            'mission' => $mission,
            'invoice' => $invoice,
            'file' => $file,
        ]);
    }

    #[Route('/{id}/invoice/{invoiceId}/{name}', name: 'invoice_show_invoice', methods: ['GET'])]
    public function invoiceShowFile(SupplierMission $supplierMission, $invoiceId, InvoiceSupplierRepository $invoiceRepository): Response
    {
        $invoice = $invoiceRepository->find($invoiceId);
        $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $supplierMission->getMission()->getId() . '/supplier/' . $invoice->getFile();
        return $this->file($file, $invoice->getFile(), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/{id}/invoice/{invoiceId}/delete', name: 'invoice_delete', methods: ['POST'])]
    public function invoiceDelete(InvoiceSupplierRepository $invoiceRepository, $invoiceId, SupplierMission $supplierMission, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $supplierMission->getId(), $request->request->get('_token'))) {
            $invoiceSupplier = $invoiceRepository->find($invoiceId);
            // suppression de la facture
            $file = $this->getParameter('kernel.project_dir') . '/facture/mission/' . $supplierMission->getMission()->getId() . '/supplier/' . $invoiceSupplier->getFile();
            if (file_exists($file)) {
                unlink($file);
            }
            $entityManager->remove($invoiceSupplier);
            $entityManager->flush();
        }
        return $this->redirectToRoute('dashboard_supplier_invoice', ['id' => $invoiceSupplier->getSupplierMission()->getId()]);
    }

}
