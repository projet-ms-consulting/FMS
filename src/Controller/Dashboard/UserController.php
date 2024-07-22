<?php

namespace App\Controller\Dashboard;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/user', name: 'dashboard_user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 8);
        $users = $userRepository->paginateUsers($page, $limit);
        return $this->render('dashboard/user/index.html.twig', [
            'users' => $users,
            'page' => $page,
            'limit' => $limit,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $userForm = $this->createForm(RegistrationFormType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $roles = $userForm->get('roles')->getData();
            if (is_string($roles)) {
                $roles = [$roles];
            }
            $user->setRoles($roles);
            $hash = $hasher->hashPassword($user, $userForm->get('plainPassword')->getData());
            $user->setPassword($hash);
            $user->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été ajouté.');
            return $this->redirectToRoute('dashboard_user_index');
        }

        return $this->render('dashboard/user/new.html.twig', [
            'user' => $user,
            'userForm' => $userForm,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('dashboard/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher, SessionInterface $session): Response
    {
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $oldEmail = $user->getEmail();
            $oldPassword = $user->getPassword();
            $roles = $userForm->get('roles')->getData();
            if (is_string($roles)) {
                $roles = [$roles];
            }
            $user->setRoles($roles);
            $user->setPassword($oldPassword);
            $entityManager->flush();
            $newEmail = $user->getEmail();

            if (($this->getUser() && $this->getUser()->getId() === $user->getId()) && ($oldEmail != $newEmail)) {
                return $this->redirectToRoute('app_logout');
            }

            $this->addFlash('success', 'L\'utilisateur a bien été modifié.');
            return $this->redirectToRoute('dashboard_user_index');
        }

        return $this->render('dashboard/user/edit.html.twig', [
            'user' => $user,
            'userForm' => $userForm,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé.');
        }

        return $this->redirectToRoute('dashboard_user_index');
    }
}
