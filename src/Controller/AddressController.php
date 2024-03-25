<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressController extends AbstractController
{
    #[Route('/address', name: 'app_address')]
    public function index(AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findAll();
        return $this->render('address/index.html.twig', [
            'address' => $address,
        ]);
    }

    #[Route('/address/new', name: 'new_address')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('app_address');
        }

        return $this->render('address/new.html.twig', [
            'addressForm' => $addressForm
        ]);
    }

    #[Route('/address/{id}', name: 'show_address')]
    public function show(AddressRepository $addressRepository, $id): Response
    {
        $address = $addressRepository->find($id);
        return $this->render('address/show.html.twig', [
            'address' => $address
        ]);
    }

    #[Route('/{id}/edit', name: 'edit_address')]
    public function edit(Request $request, Address $address, EntityManagerInterface $entityManager): Response
    {
        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);

        if ($addressForm->isSubmitted() && $addressForm->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('app_address');
        }

        return $this->render('address/edit.html.twig', [
            'addressForm' => $addressForm,
            'address' => $address,
        ]);
    }

    #[Route('/{id}', name: 'delete_address', methods: ['POST'])]
    public function delete(Address $address, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($address);
        $entityManager->flush();

        return $this->redirectToRoute('app_address');
    }
}
