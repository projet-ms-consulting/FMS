<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: InvoiceMission::class, mappedBy: 'mission', cascade: ['persist', 'remove'])]
    private Collection $invoices;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $client = null;

    #[ORM\ManyToOne(inversedBy: 'managers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?company $manager = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $finished = null;

    #[ORM\OneToMany(targetEntity: SupplierMission::class, mappedBy: 'mission', cascade: ['persist', 'remove'])]
    private Collection $supplierMission;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->supplierMission = new ArrayCollection();
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

    /**
     * @return Collection<int, InvoiceMission>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(InvoiceMission $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setMission($this);
        }

        return $this;
    }

    public function removeInvoice(InvoiceMission $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getMission() === $this) {
                $invoice->setMission(null);
            }
        }

        return $this;
    }

    public function getClient(): ?Company
    {
        return $this->client;
    }

    public function setClient(?Company $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getManager(): ?company
    {
        return $this->manager;
    }

    public function setManager(?company $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

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
     * @return Collection<int, SupplierMission>
     */
    public function getSupplierMission(): Collection
    {
        return $this->supplierMission;
    }

    public function addSupplierMission(SupplierMission $supplierMission): static
    {
        if (!$this->supplierMission->contains($supplierMission)) {
            $this->supplierMission->add($supplierMission);
            $supplierMission->setMission($this);
        }

        return $this;
    }

    public function removeSupplierMission(SupplierMission $supplierMission): static
    {
        if ($this->supplierMission->removeElement($supplierMission)) {
            // set the owning side to null (unless already changed)
            if ($supplierMission->getMission() === $this) {
                $supplierMission->setMission(null);
            }
        }

        return $this;
    }
}
