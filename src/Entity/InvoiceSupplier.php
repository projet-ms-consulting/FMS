<?php

namespace App\Entity;

use App\Repository\InvoiceSupplierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceSupplierRepository::class)]
class InvoiceSupplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $billNum = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\Column(length: 255)]
    private ?string $realFilename = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column]
    private ?bool $paid = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceSuppliers')]
    private ?SupplierMission $supplierMission = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceSuppliers')]
    private ?InvoiceMission $invoiceMission = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getRealFilename(): ?string
    {
        return $this->realFilename;
    }

    public function setRealFilename(string $realFilename): static
    {
        $this->realFilename = $realFilename;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): static
    {
        $this->paid = $paid;

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

    public function getInvoiceMission(): ?InvoiceMission
    {
        return $this->invoiceMission;
    }

    public function setInvoiceMission(?InvoiceMission $invoiceMission): static
    {
        $this->invoiceMission = $invoiceMission;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
