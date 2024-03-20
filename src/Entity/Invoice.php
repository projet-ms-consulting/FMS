<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $billNum = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Mission $mission = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?SupplierMission $supplierMission = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBillNum(): ?string
    {
        return $this->billNum;
    }

    public function setBillNum(string $billNum): static
    {
        $this->billNum = $billNum;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): static
    {
        $this->mission = $mission;

        return $this;
    }

    public function getSupplierMission(): ?SupplierMission
    {
        return $this->supplierMission;
    }

    public function setSupplierMission(?SupplierMission $supplierMission): static
    {
        $this->supplierMission = $supplierMission;

        return $this;
    }
}
