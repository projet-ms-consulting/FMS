<?php

namespace App\Controller\Dashboard;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\CompanyRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/person', name: 'dashboard_person_')]
class PersonController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PersonRepository $personRepository): Response
    {
        $persons = $personRepository->findAll();
        return $this->render('dashboard/person/index.html.twig', [
            'persons' => $persons,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $personForm = $this->createForm(PersonType::class, $person);
        $personForm->handleRequest($request);

        if ($personForm->isSubmitted() && $personForm->isValid()) {
            $person->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($person);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_person_index');
        }

        return $this->render('dashboard/person/edit.html.twig', [
            'personForm' => $personForm,
            'person' => $person,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $personForm = $this->createForm(PersonType::class, $person);
        $personForm->handleRequest($request);

        if ($personForm->isSubmitted() && $personForm->isValid()) {
            $person->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($person);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_person_index');
        }

        return $this->render('dashboard/person/new.html.twig', [
            'personForm' => $personForm,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['GET'])]
    public function delete(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        dd('test');
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $entityManager->remove($person);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_person_index');
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(PersonRepository $personRepository, $id): Response
    {
        $person = $personRepository->find($id);
        return $this->render('dashboard/person/show.html.twig', [
            'person' => $person,
        ]);
    }

}
