<?php

namespace App\Form;

use App\Entity\Address;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class AddressAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Address::class,
            'placeholder' => 'Choisissez une adresse',
            'label' => 'Adresse',
            'choice_label' => function (Address $address) {
                return $address->getFullAddress();
            },
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a')
                    ->leftJoin('a.company', 'c')
                    ->where('c IS NULL');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
