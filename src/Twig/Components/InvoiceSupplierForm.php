<?php

namespace App\Twig\Components;

use App\Entity\InvoiceSupplier;
use App\Entity\SupplierMission;
use App\Form\InvoiceSupplierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class InvoiceSupplierForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?SupplierMission $supplierMission = null;

    #[LiveProp]
    public ?InvoiceSupplier $invoice = null;

    protected function instantiateForm(): FormInterface
    {
        $invoiceSupplierData = $this->invoice instanceof InvoiceSupplier ? $this->invoice : null;

        return $this->createForm(InvoiceSupplierType::class, $invoiceSupplierData, [
            'invoiceMissionId' => $this->supplierMission->getId(),
        ]);
    }
}
