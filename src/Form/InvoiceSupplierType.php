<?php

namespace App\Form;

use App\Entity\InvoiceMission;
use App\Entity\InvoiceSupplier;
use App\Entity\SupplierMission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class InvoiceSupplierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('billNum')
            ->add('file', FileType::class, [
                'label' => 'File',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('paid', CheckboxType::class, [
                'label' => 'Paid',
                'required' => false,
            ])
            ->add('invoiceMission', EntityType::class, [
                'class' => InvoiceMission::class,
                'choice_label' => 'billNum',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceSupplier::class,
        ]);
    }
}
