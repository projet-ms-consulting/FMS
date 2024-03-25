<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        //Person
        $person = new Person();
        $person->setFirstname('John');
        $person->setLastname('Doe');
        $person->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($person);
        $personAdmin[] = $person;

        $user = new User();
        $user->setEmail('tomscherer29@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPerson($personAdmin[0]);
        $user->setPassword($this->hasher->hashPassword(
            $user,
            'password123'
        ));
        $user->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user);
        $manager->flush();
    }
}