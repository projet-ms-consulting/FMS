<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'company', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Address $address = null;

    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'company')]
    private Collection $people;

    #[ORM\OneToMany(targetEntity: SupplierMission::class, mappedBy: 'supplier')]
    private Collection $suppliers;

    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'client')]
    private Collection $clients;

    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'manager')]
    private Collection $managers;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numTva = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siren = null;

    #[ORM\Column(nullable: false)]
    private ?bool $headOffice = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCompany $type = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?self $createdBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'createdBy')]
    private Collection $companies;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $createdByUser = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'companiesUpdatedBy')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedByUser = null;

    public function __construct()
    {
        $this->people = new ArrayCollection();
        $this->suppliers = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->managers = new ArrayCollection();
        $this->companies = new ArrayCollection();
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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->setCompany($this);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getCompany() === $this) {
                $person->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SupplierMission>
     */
    public function getSuppliers(): Collection
    {
        return $this->suppliers;
    }

    public function addSupplier(SupplierMission $supplier): static
    {
        if (!$this->suppliers->contains($supplier)) {
            $this->suppliers->add($supplier);
            $supplier->setSupplier($this);
        }

        return $this;
    }

    public function removeSupplier(SupplierMission $supplier): static
    {
        if ($this->suppliers->removeElement($supplier)) {
            // set the owning side to null (unless already changed)
            if ($supplier->getSupplier() === $this) {
                $supplier->setSupplier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Mission $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setClient($this);
        }

        return $this;
    }

    public function removeClient(Mission $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getClient() === $this) {
                $client->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(Mission $manager): static
    {
        if (!$this->managers->contains($manager)) {
            $this->managers->add($manager);
            $manager->setManager($this);
        }

        return $this;
    }

    public function removeManager(Mission $manager): static
    {
        if ($this->managers->removeElement($manager)) {
            // set the owning side to null (unless already changed)
            if ($manager->getManager() === $this) {
                $manager->setManager(null);
            }
        }

        return $this;
    }

    public function getNumTva(): ?string
    {
        return $this->numTva;
    }

    public function setNumTva(?string $numTva): static
    {
        $this->numTva = $numTva;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): static
    {
        $this->siren = $siren;

        return $this;
    }

    public function isHeadOffice(): ?bool
    {
        return $this->headOffice;
    }

    public function setHeadOffice(?bool $headOffice): static
    {
        $this->headOffice = $headOffice;

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

    public function getType(): ?TypeCompany
    {
        return $this->type;
    }

    public function setType(?TypeCompany $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(self $company): static
    {
        if (!$this->companies->contains($company)) {
            $this->companies->add($company);
            $company->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCompany(self $company): static
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getCreatedBy() === $this) {
                $company->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getCreatedByUser(): ?User
    {
        return $this->createdByUser;
    }

    public function setCreatedByUser(?User $createdByUser): static
    {
        $this->createdByUser = $createdByUser;

        return $this;
    }

    public function getUpdatedByUser(): ?User
    {
        return $this->updatedByUser;
    }

    public function setUpdatedByUser(?User $updatedByUser): static
    {
        $this->updatedByUser = $updatedByUser;

        return $this;
    }
}
