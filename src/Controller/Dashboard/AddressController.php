<?php

namespace App\Controller\Dashboard;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/address', name: 'dashboard_address_')]
class AddressController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(AddressRepository $repository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $address = $repository->paginateAdresses($page, $limit);
        return $this->render('dashboard/address/index.html.twig', [ 
            'address' => $address,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setCreatedAt(new \DateTimeImmutable());
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute('dashboard_address_index');
        }
        return $this->render('dashboard/address/new.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function detail(Address $address): Response
    {
        return $this->render('dashboard/address/show.html.twig', [
            'address' => $address
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            return $this->redirectToRoute('dashboard_address_index');
        }
        return $this->render('dashboard/address/edit.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Address $address, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $em->remove($address);
            $em->flush();
        }
        return $this->redirectToRoute('dashboard_address_index');
    }
}