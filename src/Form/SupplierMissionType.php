<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Mission;
use App\Entity\SupplierMission;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierMissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la mission',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9 \-\é\è\ê\à\ç]+$/',
                        'message' => 'Le nom ne doit contenir que des lettres, des chiffres, des espaces et des tirets.'
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9 \-\.,;!"\'\(\)\[\]\/\é\è\ê\ë\à\â\ä\î\ï\ô\ö\ù\û\ü\ç]+$/',
                        'message' => 'La description contient des caractères non autorisés.'
                    ]),
                ],
            ])
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
                'label' => 'Mission',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une mission',
            ])
            ->add('supplier', EntityType::class, [
                'class' => Company::class,
                'label' => 'Fournisseur',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un Fournisseur',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->join('c.type', 't')
                        ->where('t.label = :type')
                        ->setParameter('type', 'Fournisseur');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupplierMission::class,
        ]);
    }
}
