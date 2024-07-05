<?php

namespace App\Form;

use App\Entity\Person;
use App\Entity\User;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'] ?? null;
        $currentRoles = $user ? $user->getRoles() : [];

        $builder
            ->add('email', null, [
                'label' => 'Email',
            ])
//            ->add('person', EntityType::class, [
//                'class' => Person::class,
//                'label' => 'Personne',
//                'choice_label' => function (Person $person) {
//                    return $person->getLastName() . ' ' . $person->getFirstName();
//                },
//                'placeholder' => 'Choisissez une personne',
//                'query_builder' => function (EntityRepository $er) use ($options) {
//                    $currentPerson = $options['data']->getPerson();
//
//                    $qb = $er->createQueryBuilder('p');
//                    $qb->leftJoin('p.user', 'u')
//                        ->where('u.person IS NULL');
//
//                    if ($currentPerson) {
//                        $qb->orWhere('p.id = :currentPersonId')
//                            ->setParameter('currentPersonId', $currentPerson->getId());
//                    }
//
//                    return $qb;
//                },
//            ])
            ->add('person', PersonAutocompleteField::class, [
                'data' => $options['data']->getPerson(),
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $currentPerson = $options['data']->getPerson();

                    $qb = $er->createQueryBuilder('p');
                    $qb->leftJoin('p.user', 'u')
                        ->where('u.person IS NULL');

                    if ($currentPerson) {
                        $qb->orWhere('p.id = :currentPersonId')
                            ->setParameter('currentPersonId', $currentPerson->getId());
                    }

                    return $qb;
                },
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir au moins un role']),
                ],
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Super Administrateur' => 'ROLE_SUPER_ADMIN',
                ],
                'data' => $currentRoles,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
