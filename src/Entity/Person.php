<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\OneToOne(mappedBy: 'person', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'people')]
    private ?Company $company = null;

    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'manager')]
    private Collection $managers;

    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'client')]
    private Collection $clients;

    public function __construct()
    {
        $this->managers = new ArrayCollection();
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        // set the owning side of the relation if necessary
        if ($user->getPerson() !== $this) {
            $user->setPerson($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

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
}
