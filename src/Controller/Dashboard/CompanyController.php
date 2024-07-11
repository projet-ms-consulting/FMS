<?php

namespace App\Controller\Dashboard;

use App\Entity\Address;
use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/company', name: 'dashboard_company_')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, CompanyRepository $companyRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $companies = $companyRepository->paginateCompanies($page, $limit);
        return $this->render('dashboard/company/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $companyForm = $this->createForm(CompanyType::class, $company);
        $companyForm->handleRequest($request);

        if ($companyForm->isSubmitted() && $companyForm->isValid()) {
            $data = $request->request->all()['company'];

            if ($data['checkAddress'] == 2) {
                $address = new Address();
                $address->setNbStreet($data['nbStreetNewAddress']);
                $address->setStreet($data['streetNewAddress']);
                $address->setZipCode($data['zipCodeNewAddress']);
                $address->setCity($data['cityNewAddress']);
                $address->setCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($address);
                $company->setAddress($address);
            }
            $company->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_company_index');
        }

        return $this->render('dashboard/company/new.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('dashboard/company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $companyForm = $this->createForm(CompanyType::class, $company);
        $companyForm->handleRequest($request);

        if ($companyForm->isSubmitted() && $companyForm->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('dashboard_company_index');
        }

        return $this->render('dashboard/company/edit.html.twig', [
            'company' => $company,
            'companyForm' => $companyForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_company_index');
    }

}
