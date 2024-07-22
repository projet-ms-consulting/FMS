<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nbStreet', TextType::class, [
                'label' => 'Numéro de Rue',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d+[a-zA-Z]?$/',
                        'message' => 'Le numéro de rue n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('street', TextType::class, [
                'label' => 'Voirie',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9\s\-]+$/',
                        'message' => 'Le nom de la voirie n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('zipCode', NumberType::class, [
                'label' => 'Code postal',
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
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\-]+$/',
                        'message' => 'La ville n\'est pas valide.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
