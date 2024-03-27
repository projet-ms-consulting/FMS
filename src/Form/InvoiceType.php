<?php

namespace App\Form;

use App\Entity\Invoice;
use App\Entity\Mission;
use App\Entity\SupplierMission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('billNum')
            ->add('file')
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
                'choice_label' => 'id',
            ])
            ->add('supplierMission', EntityType::class, [
                'class' => SupplierMission::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
