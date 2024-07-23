<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\InvoiceMission;
use App\Entity\InvoiceSupplier;
use App\Entity\Mission;
use App\Entity\SupplierMission;
use App\Entity\TypeCompany;
use App\Entity\Person;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
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

        // SuperAdmin
        $superAdmin = new Person();
        $superAdmin->setFirstName($this->faker->firstName());
        $superAdmin->setLastName($this->faker->lastName());
        $superAdmin->setPhone($this->faker->phoneNumber());
        $superAdmin->setCreatedAt(new DateTimeImmutable());
        $manager->persist($superAdmin);

        // User (SuperAdmin)
        $userSuperAdmin = new User();
        $userSuperAdmin->setEmail('superadmin@admin.fr');
        $hash = $this->hasher->hashPassword($userSuperAdmin, 'admin');
        $userSuperAdmin->setPassword($hash);
        $userSuperAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $userSuperAdmin->setPerson($superAdmin);
        $userSuperAdmin->setCreatedAt(new DateTimeImmutable());
        $manager->persist($userSuperAdmin);

        $manager->flush();
    }
}
