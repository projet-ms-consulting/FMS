<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\TypeCompany;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'entreprise',
            ])
            ->add('numTva', TextType::class, [
                'label' => 'Numéro de TVA',
                'required' => false,
            ])
            ->add('siret', TextType::class, [
                'label' => 'Siret',
                'required' => false,
            ])
            ->add('siren', TextType::class, [
                'label' => 'Siren',
                'required' => false,
            ])
            ->add('headOffice', ChoiceType::class, [
                'label' => 'Siège social',
                'data' => false,
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
            ])
            ->add('type', EntityType::class, [
                'class' => TypeCompany::class,
                'label' => 'Role',
                'choice_label' => 'label',
                'placeholder' => 'Choisissez un role',
            ])
            ->add('checkAddress', ChoiceType::class, [
                    'label' => 'Avez vous déja créer une adresse ?',
                    'placeholder' => 'Choisissez une option',
                    'placeholder_attr' => ['hidden' => true],
                    'mapped' => false,
                    'choices' => [
                        'Je ne veux pas d\'adresse' => null,
                        'Oui' => true,
                        'Non' => false,
                    ],
                ])
            ->addDependent('address', 'checkAddress', function (DependentField $field, ?bool $checkAddress) {
                if ($checkAddress === true) {
                    $field
                        ->add(AddressAutocompleteField::class, [
                        'class' => Address::class,
                        'label' => 'Adresse',
                        'choice_label' => function (Address $address) {
                            return $address->getFullAddress();
                        },
                        'placeholder' => 'Choisissez une adresse',
                        'required' => true,
                        'autocomplete' => true,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('a')
                                ->orderBy('a.city', 'ASC');
                        },
                    ]);
                }
            })
            ->addDependent('nbStreetNewAddress', 'checkAddress', function (DependentField $field, ?bool $checkAddress) {
                if ($checkAddress === false) {
                    $field
                        ->add(TextType::class, [
                            'label' => 'Numéro de Rue',
                            'mapped' => false,
                            'required' => true,
                        ]);
                }
            })
            ->addDependent('streetNewAddress', 'checkAddress', function (DependentField $field, ?bool $checkAddress) {
                if ($checkAddress === false) {
                    $field
                        ->add(TextType::class, [
                            'label' => 'Voirie',
                            'mapped' => false,
                            'required' => true,
                        ]);
                }
            })
            ->addDependent('zipCodeNewAddress', 'checkAddress', function (DependentField $field, ?bool $checkAddress) {
                if ($checkAddress === false) {
                    $field
                        ->add(TextType::class, [
                            'label' => 'Code postal',
                            'mapped' => false,
                            'required' => true,
                        ]);
                }
            })
            ->addDependent('cityNewAddress', 'checkAddress', function (DependentField $field, ?bool $checkAddress) {
                if ($checkAddress === false) {
                    $field
                        ->add(TextType::class, [
                            'label' => 'Ville',
                            'mapped' => false,
                            'required' => true,
                        ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
