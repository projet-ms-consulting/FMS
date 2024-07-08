<?php

namespace App\Form;

use App\Entity\InvoiceMission;
use App\Entity\InvoiceSupplier;
use App\Entity\SupplierMission;
use App\Repository\InvoiceSupplierRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class InvoiceSupplierType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder = new DynamicFormBuilder($builder);

        $deadlineData = isset($options['data']) && $options['data']->getDeadline() ? $options['data']->getDeadline() : new \DateTime("now + 1 week");
        $paymentDateData = isset($options['data']) && $options['data']->getPaymentDate() ? $options['data']->getPaymentDate() : new \DateTime("now");

        $invoiceMissionId = $options['invoiceMissionId'];

        dump($invoiceMissionId);

        $builder
            ->add('billNum', TextType::class, [
                'label' => 'Numéro de facture',
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
                'label' => 'Fichier (PDF)',
                'help' => $options['page'] === 'edit' ? 'Laissez vide pour conserver le fichier actuel' : null,
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
            ->add('invoiceMission', InvoiceMissionAutocompleteField::class, [
                'label' => 'Facture liée',
                'class' => InvoiceMission::class,
                'choice_label' => 'billNum',
                'required' => false,
                'placeholder' => 'Choisissez une facture à liée',
                'query_builder' => function (EntityRepository $er) use ($invoiceMissionId) {
                    return $er->createQueryBuilder('i')
                        ->where('i.mission = :id')
                        ->setParameter('id', 2);
                },
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix unitaire',
                'currency' => null,
                'invalid_message' => 'Veuillez entrer un montant valide',
                'attr' => [
                    'placeholder' => '0.00',
                ],
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'minMessage' => 'Le prix ne peut pas être négatif',
                    ]),
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'invalid_message' => 'Veuillez entrer un nombre valide',
                'empty_data' => '1',
                'attr' => [
                    'placeholder' => '1',
                ],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'minMessage' => 'La quantité ne peut pas être inférieure à 1',
                    ]),
                ],
            ])
            ->add('unit', ChoiceType::class, [
                'label' => 'Unité',
                'empty_data' => 'Heure',
                'choices'  => [
                    'Heure(s)' => 'Heure',
                    'Jour(s)' => 'Jour',
                    'Forfait' => 'Forfait',
                ],
            ])
            ->add('tva', ChoiceType::class, [
                'label' => 'TVA (%)',
                'empty_data' => 20,
                'data' => 20,
                'attr' => [
                    'placeholder' => '20',
                ],
                'choices'  => [
                    '0%' => 0,
                    '5.5%' => 5.5,
                    '10%' => 10,
                    '20%' => 20,
                ],
            ])
            ->add('paymentTypology', ChoiceType::class, [
                'label' => 'Typologie de paiement',
                'choices'  => [
                    'Fin de mois' => 'Fin de mois',
                    '45 jours fin de mois' => '45 jours fin de mois',
                    '60 jours' => '60 jours',
                    '90 jours' => '90 jours',
                ],
            ])
            ->add('issueDate', DateType::class, [
                'label' => 'Date d\'émission de la facture',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime("now"),
            ])
            ->add('paid', ChoiceType::class, [
                'label' => 'Payé ?',
                'choices'  => [
                    'Non' => false,
                    'Oui' => true,
                ],
            ])
            ->addDependent('deadline','paid', function (DependentField $field, ?bool $paid) use ($deadlineData, $options) {
                if (!$paid) {
                    $field->add(DateType::class, [
                        'label' => 'Date limite de paiement de la facture',
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                        'required' => false,
                        'attr' => [
                            'value' => $deadlineData->format('Y-m-d'),
                        ],
                        'constraints' => [
                            new Range([
                                'min' => new \DateTime('-1 month'),
                                'minMessage' => 'La date ne peut pas être antérieure à un mois avant aujourd\'hui. (minimum : ' . (new \DateTime('-1 month'))->format('d-m-Y') . ')',
                            ]),
                        ],
                    ]);
                }
            })
            ->addDependent('paymentDate', 'paid', function (DependentField $field, ?bool $paid) use ($paymentDateData, $options) {
                if ($paid) {
                    $field->add(DateType::class, [
                        'label' => 'Date de payement de la facture',
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd',
                        'required' => false,
                        'attr' => [
                            'value' => $paymentDateData->format('Y-m-d'),
                        ],
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceSupplier::class,
            'page' => null,
            'invoiceMissionId' => null,
        ]);
    }
}
