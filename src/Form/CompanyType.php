<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\TypeCompany;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('numTva')
            ->add('siret')
            ->add('siren')
            ->add('headOffice')
            ->add('type', EntityType::class, [
                'class' => TypeCompany::class,
                'choice_label' => 'label',
                'placeholder' => 'Chosissez un type',
            ])
            ->add('address', EntityType::class, [
                'class' => Address::class,
                'choice_label' => 'street',
                'placeholder' => 'Chosissez une adresse',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->leftJoin('a.company', 'c')
                        ->where('c.address IS NULL');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
