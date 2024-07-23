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
        for ($i = 0; $i < 30; $i++) {
            $address = new Address();
            $address->setNbStreet($this->faker->buildingNumber());
            $address->setStreet($this->faker->streetName());
            $address->setZipCode($this->faker->postcode());
            $address->setCity($this->faker->city());
            $address->setCreatedAt(new DateTimeImmutable());
            $manager->persist($address);
            $listAddress[] = $address;
        }

        //TypeCompany
        $types = ['Admin', 'Client', 'Fournisseur'];
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
        for ($i = 0; $i < 20; $i++) {
            $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
            $company = new Company();
            $company->setAddress($listAddress[$i]);
            $company->setType($listCompanyType[array_rand($listCompanyType)]);
            $company->setName($this->faker->company());
            $company->setNumTva($randomTva);
            $company->setSiren($this->faker->siren());
            $company->setSiret($this->faker->siret());
            $company->setHeadOffice($this->faker->boolean());
            $company->setCreatedAt($date);
            $company->setCreatedBy($company);
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

        // Admin
        $admin = new Person();
        $admin->setFirstName($this->faker->firstName());
        $admin->setLastName($this->faker->lastName());
        $admin->setPhone($this->faker->phoneNumber());
        $admin->setCreatedAt($date);
        $manager->persist($admin);

        // Person
        for ($i = 0; $i < 20; $i++) {
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
        $userSuperAdmin = new User();
        $userSuperAdmin->setEmail('superadmin@admin.fr');
        $hash = $this->hasher->hashPassword($userSuperAdmin, 'admin');
        $userSuperAdmin->setPassword($hash);
        $userSuperAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $userSuperAdmin->setPerson($superAdmin);
        $userSuperAdmin->setCreatedAt($date);
        $manager->persist($userSuperAdmin);

        // User (Admin)
        $userAdmin = new User();
        $userAdmin->setEmail('admin@admin.fr');
        $hash = $this->hasher->hashPassword($userAdmin, 'admin');
        $userAdmin->setPassword($hash);
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setPerson($admin);
        $userAdmin->setCreatedAt($date);
        $manager->persist($userAdmin);

        for ($i = 0; $i < count($listCompany); $i++) {
            $company = $listCompany[$i];
            $company->setCreatedByUser($userAdmin);
            $manager->persist($company);
        }

        // User (User)
        for ($i = 0; $i < 20; $i++) {
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

        // Filter companies with the type 'Client'
        $clientCompanies = array_filter($listCompany, function ($company) {
            return $company->getType()->getLabel() === 'Client';
        });

        // Filter companies with the type 'Fournisseur'
        $fournisseurCompanies = array_filter($listCompany, function ($company) {
            return $company->getType()->getLabel() === 'Fournisseur';
        });

        // Filter companies with the type 'Admin'
        $adminCompanies = array_filter($listCompany, function ($company) {
            return $company->getType()->getLabel() === 'Admin';
        });

        // Mission
        for ($i = 0; $i < 10; $i++) {
            $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
            $mission = new Mission();
            $mission->setName($this->faker->word());
            $mission->setDescription($this->faker->sentence(30));
            $mission->setClient($clientCompanies[array_rand($clientCompanies)]);
            $mission->setManager($adminCompanies[array_rand($adminCompanies)]);
            $mission->setFinished($this->faker->boolean());
            $mission->setCreatedAt($date);
            $manager->persist($mission);
            $listMission[] = $mission;

            // création de factures pour mission
            $tva = [0, 2.1, 5.5, 10, 20];
            $paymentTypology = ['comptant', 'fin de mois', '45 jours fin de mois', '60 jours', '90 jours'];
            $unite = ['forfait', 'heure', 'jour'];
            for ($j = 0; $j < 10; $j++) {
                $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
                $deadline = new DateTimeImmutable($this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s'));
                $paymentDate = new DateTimeImmutable($this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s'));
                $issueDate = new DateTimeImmutable($this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s'));
                $type = ['Facture', 'Devis', 'Bon de commande'];
                $invoice = new InvoiceMission();
                $invoice->setMission($mission);
                $invoice->setBillNum($this->faker->ean8());
                $invoice->setFile($this->faker->word()  . '.pdf');
                $invoice->setRealFilename($this->faker->word());
                $invoice->setDeadline($deadline);
                $invoice->setPaid($this->faker->boolean());
                $invoice->setType($type[array_rand($type)]);
                if ($invoice->isPaid()) {
                    $invoice->setPaymentDate($paymentDate);
                }
                $invoice->setTva($tva[array_rand($tva)]);
                $invoice->setPrice($this->faker->randomFloat(2, 50, 500));
                $invoice->setQuantity($this->faker->randomNumber(2));
                $invoice->setUnit($unite[array_rand($unite)]);
                $invoice->setPaymentTypology($paymentTypology[array_rand($paymentTypology)]);
                $invoice->setIssueDate($issueDate);
                $invoice->setCreatedAt($date);
                $manager->persist($invoice);
            }
        }

        $manager->flush();

        // Mission Fournisseur
        for ($i = 0; $i < 20; $i++) {
            $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
            $supplierMission = new SupplierMission();
            $supplierMission->setSupplier($fournisseurCompanies[array_rand($fournisseurCompanies)]);
            $supplierMission->setMission($listMission[array_rand($listMission)]);
            $supplierMission->setFinished($this->faker->boolean());
            $supplierMission->setName($this->faker->word());
            $supplierMission->setDescription($this->faker->sentence(30));
            $supplierMission->setCreatedAt($date);
            $manager->persist($supplierMission);
            $listSupplierMission[] = $supplierMission;

            // Création de factures pour mission fournisseur
            for ($j = 0; $j < 10; $j++) {
                $date = new DateTimeImmutable($this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'));
                $deadline = new DateTimeImmutable($this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s'));
                $paymentDate = new DateTimeImmutable($this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s'));
                $issueDate = new DateTimeImmutable($this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s'));
                $type = ['Facture', 'Devis', 'Bon de commande'];
                $invoice = new InvoiceSupplier();
                $invoice->setBillNum($this->faker->ean8());
                $invoice->setFile($this->faker->word()  . '.pdf');
                $invoice->setRealFilename($this->faker->word());
                $invoice->setDeadline($deadline);
                $invoice->setPaid($this->faker->boolean());
                $invoice->setType($type[array_rand($type)]);
                if ($invoice->isPaid()) {
                    $invoice->setPaymentDate($paymentDate);
                }
                $invoice->setTva($tva[array_rand($tva)]);
                $invoice->setPrice($this->faker->randomFloat(2, 50, 500));
                $invoice->setQuantity($this->faker->randomNumber(2));
                $invoice->setUnit($unite[array_rand($unite)]);
                $invoice->setPaymentTypology($paymentTypology[array_rand($paymentTypology)]);
                $invoice->setIssueDate($issueDate);
                $invoice->setSupplierMission($supplierMission);
                $invoice->setCreatedAt($date);
                $manager->persist($invoice);
            }
        }

        $manager->flush();
    }
}
