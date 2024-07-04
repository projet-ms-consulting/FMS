<?php

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class InvoiceMission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $billNum = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Mission $mission = null;

    #[ORM\Column(length: 255)]
    private ?string $realFilename = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column]
    private ?bool $paid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $paymentDate = null;

    #[ORM\OneToMany(targetEntity: InvoiceSupplier::class, mappedBy: 'invoiceMission')]
    private Collection $invoiceSuppliers;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->invoiceSuppliers = new ArrayCollection();
    }

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

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

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * @return Collection<int, InvoiceSupplier>
     */
    public function getInvoiceSuppliers(): Collection
    {
        return $this->invoiceSuppliers;
    }

    public function addInvoiceSupplier(InvoiceSupplier $invoiceSupplier): static
    {
        if (!$this->invoiceSuppliers->contains($invoiceSupplier)) {
            $this->invoiceSuppliers->add($invoiceSupplier);
            $invoiceSupplier->setInvoiceMission($this);
        }

        return $this;
    }

    public function removeInvoiceSupplier(InvoiceSupplier $invoiceSupplier): static
    {
        if ($this->invoiceSuppliers->removeElement($invoiceSupplier)) {
            // set the owning side to null (unless already changed)
            if ($invoiceSupplier->getInvoiceMission() === $this) {
                $invoiceSupplier->setInvoiceMission(null);
            }
        }

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
