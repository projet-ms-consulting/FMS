<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\User;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password'],
                'required' => $options['page'] === 'edit' ? false : true,
                'empty_data' => '',
            ])
            ->add('person', EntityType::class, [
                'class' => Person::class,
                'choice_label' => function (Person $person) {
                    return $person->getLastName() . ' ' . $person->getFirstName();
                },
                'placeholder' => 'Chosissez une personne',
                'query_builder' => function (PersonRepository $pr) use ($options) {
                    if ($options['page'] === 'edit') {
                        return $pr->createQueryBuilder('p')
                            ->leftJoin('p.user', 'u');
                    }

                    return $pr->createQueryBuilder('p')
                        ->leftJoin('p.user', 'u')
                        ->where('u.person IS NULL');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'page' => null, // Add this line
        ]);
    }
}
