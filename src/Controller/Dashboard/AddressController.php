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
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findAll();
        return $this->render('dashboard/address/index.html.twig', [
            'address' => $address,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('dashboard/address/new.html.twig', [
            'addressForm' => $addressForm
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(AddressRepository $addressRepository, $id): Response
    {
        $address = $addressRepository->find($id);
        return $this->render('dashboard/address/show.html.twig', [
            'address' => $address
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, Address $address, EntityManagerInterface $entityManager): Response
    {
        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('app_address');
        }

        return $this->render('dashboard/address/edit.html.twig', [
            'addressForm' => $addressForm,
            'address' => $address,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Address $address, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($address);
        $entityManager->flush();

        return $this->redirectToRoute('dashboard_address_index');
    }
}