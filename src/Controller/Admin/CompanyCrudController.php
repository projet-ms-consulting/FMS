<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CompanyCrudController extends AbstractCrudController
{
    use Trait\AddDetails;

    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Company name'),
            TextField::new('num_tva', 'TVA number')->onlyOnDetail(),
            TextField::new('siret', 'SIRET number')->onlyOnDetail(),
            TextField::new('siren', 'SIREN number')->onlyOnDetail(),
            AssociationField::new('address', 'Address'),
            AssociationField::new('users', 'Users')->onlyOnIndex(),
            ArrayField::new('users', 'Users')->onlyOnDetail(),
            BooleanField::new('head_office', 'Head office'),
            DateTimeField::new('created_at', 'Creation date')->hideOnForm(),
        ];
    }
}
