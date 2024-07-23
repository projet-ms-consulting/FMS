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
use Symfony\Component\Validator\Constraints\Regex;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        // Accéder à l'entité Company
        $company = $options['data'] ?? null;

        // Déterminer si l'entreprise a une adresse
        $hasAddress = null;
        if ($company && $company->getAddress()) {
            $hasAddress = true;
        }

        $hasheadOffice = null;
        if ($company && $company->isHeadOffice()) {
            $hasheadOffice = true;
        } else {
            $hasheadOffice = false;
        }

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9\s\-]+$/',
                        'message' => 'Le nom de l\'entreprise n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('numTva', TextType::class, [
                'label' => 'Numéro de TVA',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d{11}$/',
                        'message' => 'Le numéro de TVA n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('siret', TextType::class, [
                'label' => 'Siret',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d{14}$/',
                        'message' => 'Le numéro de Siret n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('siren', TextType::class, [
                'label' => 'Siren',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d{9}$/',
                        'message' => 'Le numéro de Siren n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('headOffice', ChoiceType::class, [
                'label' => 'Siège social',
                'data' => $hasheadOffice,
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
                    'label' => 'Avez vous déja créé une adresse ?',
                    'placeholder' => 'Choisissez une option',
                    'placeholder_attr' => ['hidden' => true],
                    'mapped' => false,
                    'data' => $hasAddress,
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
                            'constraints' => [
                                new Regex([
                                    'pattern' => '/^\d+[a-zA-Z]?$/',
                                    'message' => 'Le numéro de rue n\'est pas valide.',
                                ]),
                            ],
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
                            'constraints' => [
                                new Regex([
                                    'pattern' => '/^[a-zA-Z0-9\s\-]+$/',
                                    'message' => 'Le nom de la voirie n\'est pas valide.',
                                ]),
                            ],
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
                            'attr' => [
                                'min' => 10000,
                                'max' => 99999
                            ],
                            'constraints' => [
                                new Regex([
                                    'pattern' => '/^\d{5}$/',
                                    'message' => 'Le code postal n\'est pas valide.',
                                ]),
                            ],
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
                            'constraints' => [
                                new Regex([
                                    'pattern' => '/^[a-zA-Z\s\-]+$/',
                                    'message' => 'La ville n\'est pas valide.',
                                ]),
                            ],
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
