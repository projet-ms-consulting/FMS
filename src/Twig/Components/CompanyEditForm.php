<?php

namespace App\Twig\Components;

use App\Entity\Company;
use App\Form\CompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class CompanyEditForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(fieldName:"companyData")]
    public ?Company $company = null;

    protected function instantiateForm(): FormInterface
    {
        $companyData = $this->company instanceof Company ? $this->company : null;
        return $this->createForm(CompanyType::class, $companyData);
    }
}
