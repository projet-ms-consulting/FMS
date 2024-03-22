<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AddressController extends AbstractController
{
    #[Route('/address', name: 'app_address')]
    public function index(): Response
    {
        return $this->render('address/index.html.twig', [
            'controller_name' => 'AddressController',
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
        }

        return $this->render('address/new.html.twig', [
            'addressForm' => $addressForm
        ]);
    }
}
