<?php

namespace App\Entity;

use App\Repository\SupplierMissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplierMissionRepository::class)]
class SupplierMission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'suppliers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $supplier = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $finished = null;

    #[ORM\OneToMany(targetEntity: InvoiceSupplier::class, mappedBy: 'supplierMission', cascade: ['persist', 'remove'])]
    private Collection $invoiceSuppliers;

    #[ORM\ManyToOne(inversedBy: 'supplierMission')]
    private ?Mission $mission = null;

    public function __construct()
    {
        $this->invoiceSuppliers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSupplier(): ?Company
    {
        return $this->supplier;
    }

    public function setSupplier(?Company $supplier): static
    {
        $this->supplier = $supplier;

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

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): static
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * @return Collection<int, InvoiceSupplier>
     */
    public function getInvoices(): Collection
    {
        return $this->invoiceSuppliers;
    }

    public function addInvoice(InvoiceSupplier $invoiceSupplier): static
    {
        if (!$this->invoiceSuppliers->contains($invoiceSupplier)) {
            $this->invoiceSuppliers->add($invoiceSupplier);
            $invoiceSupplier->setSupplierMission($this);
        }

        return $this;
    }

    public function removeInvoice(InvoiceSupplier $invoiceSupplier): static
    {
        if ($this->invoiceSuppliers->removeElement($invoiceSupplier)) {
            // set the owning side to null (unless already changed)
            if ($invoiceSupplier->getSupplierMission() === $this) {
                $invoiceSupplier->setSupplierMission(null);
            }
        }

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

}
