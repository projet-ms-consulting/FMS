<?php

namespace App\Controller\Dashboard;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/address', name: 'address_')]
class AddressController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(AddressRepository $repository, Request $request)
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
    public function new(Request $request, EntityManagerInterface $em)
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute('address_index');
        }
        return $this->render('dashboard/address/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function detail(Address $address)
    {
        return $this->render('dashboard/address/detail.html.twig', [
            'address' => $address
        ]);
    }


    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Address $address, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('address_index');
        }
        return $this->render('dashboard/address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Address $address, EntityManagerInterface $em)
    {
        $em->remove($address);
        $em->flush();
        return $this->redirectToRoute('address_index');
    }
}