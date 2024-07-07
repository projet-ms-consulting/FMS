<?php

namespace App\Form;

use App\Entity\InvoiceMission;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class InvoiceMissionAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Facture liée',
            'class' => InvoiceMission::class,
            'choice_label' => 'billNum',
            'required' => false,
            'placeholder' => 'Choisissez une facture à liée',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('im')

                    ->orderBy('im.billNum', 'ASC');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
