<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $dateTimeNow = new \DateTime();
        // Address
        for ($i = 0; $i < 5; ++$i) {
            $address = new Address();
            $address->setNbStreet($this->faker->buildingNumber);
            $address->setStreet($this->faker->streetName);
            $address->setCity($this->faker->city);
            $address->setZipCode($this->faker->postcode);
            $address->setCreatedAt($dateTimeNow);
            $manager->persist($address);
            $listAddress[] = $address;
        }

        $types = ['SARL', 'Association', 'TPE', 'Organisation'];
        // Company
        for ($i = 0; $i < 5; ++$i) {
            $company = new Company();
            $company->setName($this->faker->company());
            $company->setSiren($this->faker->siren());
            $company->setSiret($this->faker->siret());
            $company->setType($types[array_rand($types)]);
            $company->setNumTva(rand(10000, 99999));
            $company->setHeadOffice($this->faker->boolean);
            $company->setAddress($listAddress[$i]);
            $company->setCreatedAt($dateTimeNow);
            $manager->persist($company);
            $listCompany[] = $company;
        }

        // Admin
        $admin = new User();
        $admin->setEmail('admin@admin.fr');
        $hash = $this->hasher->hashPassword($admin, 'admin');
        $admin->setPassword($hash);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCompany($listCompany[0]);
        $admin->setCreatedAt($dateTimeNow);
        $manager->persist($admin);

        // User
        for ($i = 0; $i < 4; ++$i) {
            $user = new User();
            $user->setEmail('user'.$i + 1 .'@user.fr');
            $hash = $this->hasher->hashPassword($user, 'user');
            $user->setPassword($hash);
            $user->setRoles(['ROLE_USER']);
            $user->setCompany($listCompany[$i + 1]);
            $user->setLastname($this->faker->lastName());
            $user->setFirstname($this->faker->firstName);
            $user->setPhone($this->faker->phoneNumber);
            $user->setCreatedAt($dateTimeNow);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
