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
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PersonRepository $personRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $persons = $personRepository->paginatePerson($page, $limit);
        return $this->render('dashboard/person/index.html.twig', [
            'persons' => $persons,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $personForm = $this->createForm(PersonType::class, $person);
        $personForm->handleRequest($request);

        if ($personForm->isSubmitted() && $personForm->isValid()) {
            $person->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($person);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_person_index');
        }

        return $this->render('dashboard/person/new.html.twig', [
            'person' => $person,
            'personForm' => $personForm,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Person $person): Response
    {
        return $this->render('dashboard/person/show.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $entityManager->remove($person);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_person_index');
    }

}
