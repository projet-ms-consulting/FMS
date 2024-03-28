<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\TypeCompany;
use App\Entity\Person;
use App\Entity\User;
use DateTimeImmutable;
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

    public function load(ObjectManager $manager)
    {
        //address
        for ($i = 0; $i < 6; $i++) {
            $address = new Address();
            $address->setNbStreet($this->faker->buildingNumber());
            $address->setStreet($this->faker->streetName());
            $address->setZipCode($this->faker->postcode());
            $address->setCity($this->faker->city());
            $manager->persist($address);
            $listAddress[] = $address;
        }

        //TypeCompany
        $types = ['Manager', 'Client', 'Supplier'];
        $i = 1;
        foreach ($types as $type) {
            $companyType = new TypeCompany();
            $companyType->setId($i);
            $companyType->setLabel($type);
            $manager->persist($companyType);
            $listCompanyType[] = $companyType;
            $i++;
        }

        //company
        $randomTva = 'FR' . rand(100000000, 999999999);
        for ($i = 0; $i < 6; $i++) {
            $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
            $company = new Company();
            $company->setAddress($listAddress[$i]);
            $company->setType($listCompanyType[array_rand($listCompanyType)]);
            $company->setName($this->faker->company());
            $company->setNumTva($randomTva);
            $company->setSiren($this->faker->siren());
            $company->setSiret($this->faker->siret());
            $company->setCreatedAt($date);
            $manager->persist($company);
            $listCompany[] = $company;
        }

        // SuperAdmin
        $superAdmin = new Person();
        $superAdmin->setFirstName($this->faker->firstName());
        $superAdmin->setLastName($this->faker->lastName());
        $superAdmin->setPhone($this->faker->phoneNumber());
        $superAdmin->setCreatedAt($date);
        $manager->persist($superAdmin);

        // Person
        for ($i = 0; $i < 30; $i++) {
            $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
            $person = new Person();
            $person->setFirstName($this->faker->firstName());
            $person->setLastName($this->faker->lastName());
            $person->setPhone($this->faker->phoneNumber());
            $person->setCompany($listCompany[array_rand($listCompany)]);
            $person->setCreatedAt($date);
            $manager->persist($person);
            $listPerson[] = $person;
        }

        // User (SuperAdmin)
        $user = new User();
        $user->setEmail('admin@admin.fr');
        $hash = $this->hasher->hashPassword($user, 'admin');
        $user->setPassword($hash);
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPerson($superAdmin);
        $user->setCreatedAt($date);
        $manager->persist($user);

        // User (User)
        for ($i = 0; $i < 30; $i++) {
            $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
            $user = new User();
            $user->setEmail('user' . $i + 1 . '@user.fr');
            $hash = $this->hasher->hashPassword($user, 'user');
            $user->setPassword($hash);
            $user->setRoles(['ROLE_USER']);
            $user->setPerson($listPerson[$i]);
            $user->setCreatedAt($date);
            $manager->persist($user);
            $listUser[] = $user;
        }

        $manager->flush();
    }
}