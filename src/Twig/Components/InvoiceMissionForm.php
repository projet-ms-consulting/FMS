<?php

namespace App\Twig\Components;

use App\Entity\InvoiceMission;
use App\Entity\Mission;
use App\Form\InvoiceMissionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class InvoiceMissionForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?Mission $mission = null;

    #[LiveProp]
    public ?InvoiceMission $invoice = null;

    protected function instantiateForm(): FormInterface
    {

        $invoiceMissionData = $this->invoice instanceof InvoiceMission ? $this->invoice : null;

        return $this->createForm(InvoiceMissionType::class, $invoiceMissionData);
    }
}
