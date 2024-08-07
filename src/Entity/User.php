<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il y a dejà un compte avec cette adresse email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(inversedBy: 'user')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Person $person = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Company>
     */
    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'createdByUser')]
    private Collection $companiesCreatedBy;

    /**
     * @var Collection<int, Company>
     */
    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'updatedByUser')]
    private Collection $companiesUpdatedBy;

    public function __construct()
    {
        $this->companiesCreatedBy = new ArrayCollection();
        $this->companiesUpdatedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(Person $person): static
    {
        $this->person = $person;

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

    /**
     * @return Collection<int, Company>
     */
    public function getCompaniesCreatedBy(): Collection
    {
        return $this->companiesCreatedBy;
    }

    public function addCompaniesCreatedBy(Company $companiesCreatedBy): static
    {
        if (!$this->companiesCreatedBy->contains($companiesCreatedBy)) {
            $this->companiesCreatedBy->add($companiesCreatedBy);
            $companiesCreatedBy->setCreatedByUser($this);
        }

        return $this;
    }

    public function removeCompaniesCreatedBy(Company $companiesCreatedBy): static
    {
        if ($this->companiesCreatedBy->removeElement($companiesCreatedBy)) {
            // set the owning side to null (unless already changed)
            if ($companiesCreatedBy->getCreatedByUser() === $this) {
                $companiesCreatedBy->setCreatedByUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompaniesUpdatedBy(): Collection
    {
        return $this->companiesUpdatedBy;
    }

    public function addCompaniesUpdatedBy(Company $companiesUpdatedBy): static
    {
        if (!$this->companiesUpdatedBy->contains($companiesUpdatedBy)) {
            $this->companiesUpdatedBy->add($companiesUpdatedBy);
            $companiesUpdatedBy->setUpdatedByUser($this);
        }

        return $this;
    }

    public function removeCompaniesUpdatedBy(Company $companiesUpdatedBy): static
    {
        if ($this->companiesUpdatedBy->removeElement($companiesUpdatedBy)) {
            // set the owning side to null (unless already changed)
            if ($companiesUpdatedBy->getUpdatedByUser() === $this) {
                $companiesUpdatedBy->setUpdatedByUser(null);
            }
        }

        return $this;
    }
}
