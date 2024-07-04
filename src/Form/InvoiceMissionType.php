<?php

namespace App\Form;

use App\Entity\InvoiceMission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class InvoiceMissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Définition de la fonction de validation
        $validator = function($date, ExecutionContextInterface $context) {
            $oneMonthAgo = new \DateTime('-1 month');
            if ($date < $oneMonthAgo) {
                $context->buildViolation('La date ne peut pas être antérieure à un mois avant aujourd\'hui. (minimum : ' . $oneMonthAgo->format('d-m-Y') . ')')
                    ->addViolation();
            }
        };

        $constraints = [];
        if ($options['page'] !== 'edit') {
            $constraints[] = new Callback([
                'callback' => $validator,
            ]);
        }

        $builder
            ->add('billNum', null, [
                'label' => 'Numéro de facture'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices'  => [
                    'Facture' => 'Facture',
                    'Devis' => 'Devis',
                    'Bon de commande' => 'Bon de commande',
                ],
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => $options['page'] !== 'edit',
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un document PDF valide',
                    ])
                ],
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date d\'éxpiration',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
                'constraints' => $constraints,
                'data' => $options['data']->getDeadline() ?? new \DateTime("now + 1 week"),
            ])
            ->add('paid', CheckboxType::class, [
                'label' => 'Payé ?',
                'required' => false,
                'attr' => [
                    'class' => 'paid mr-2',
                    'onclick' => 'checkPaid();',
                ],
            ])
            ->add('paymentDate', DateType::class, [
                'label' => 'Date de payement de la facture',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
                'data' => $options['data']->getPaymentDate() ?? new \DateTime("now"),
                'row_attr' => [
                    'class' => 'paymentDate mb-6',
                    'hidden' => true,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceMission::class,
            'page' => null,
        ]);
    }
}
