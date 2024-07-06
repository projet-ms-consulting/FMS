<?php

namespace App\Form;

use App\Entity\Person;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PersonAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Person::class,
            'label' => 'Personne',
            'choice_label' => function (Person $person) {
                return $person->getLastName() . ' ' . $person->getFirstName();
            },
            'placeholder' => 'Choisissez une personne',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->leftJoin('p.user', 'u')
                    ->where('u.person IS NULL');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
