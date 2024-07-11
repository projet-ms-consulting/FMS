<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Mission;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom'
            ])
            ->add('description', null, [
                'label' => 'Description'
            ])
            ->add('client', EntityType::class, [
                'class' => company::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un client',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->join('c.type', 't')
                        ->where('t.label = :type')
                        ->setParameter('type', 'client');
                },
            ])
            ->add('manager', EntityType::class, [
                'class' => company::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un manager',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $currentManager = $options['data']->getManager();

                    $qb = $er->createQueryBuilder('c');
                    $qb->leftJoin('c.type', 't')
                        ->where('t.label = :type')
                        ->setParameter('type', 'Admin');

                    if ($currentManager) {
                        $qb->orWhere('c.id = :currentManagerId')
                            ->setParameter('currentManagerId', $currentManager->getId());
                    }

                    return $qb;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
