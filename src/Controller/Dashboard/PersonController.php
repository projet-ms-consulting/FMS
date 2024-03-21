<?php

namespace App\Controller\Dashboard;

use App\Repository\CompanyRepository;
use App\Repository\PersonRepository;
use http\Env\Request;
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

    #[Route('/{id}', name: 'show')]
    public function show(PersonRepository $personRepository, $id): Response
    {
        $person = $personRepository->find($id);
        return $this->render('dashboard/person/show.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(PersonRepository $personRepository, CompanyRepository $companyRepository, $id): Response
    {
        $companies = $companyRepository->findAll();
        $person = $personRepository->find($id);

        return $this->render('dashboard/person/edit.html.twig', [
            'person' => $person,
            'companies' => $companies,
        ]);
    }

}
