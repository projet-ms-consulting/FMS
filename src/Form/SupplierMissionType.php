<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Mission;
use App\Entity\SupplierMission;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierMissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom'
            ])
            ->add('description', null, [
                'label' => 'Description'
            ])
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
                'label' => 'Mission',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une mission',
            ])
            ->add('supplier', EntityType::class, [
                'class' => Company::class,
                'label' => 'Fournisseur',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un Fournisseur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->join('c.type', 't')
                        ->where('t.label = :type')
                        ->setParameter('type', 'Fournisseur');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupplierMission::class,
        ]);
    }
}
