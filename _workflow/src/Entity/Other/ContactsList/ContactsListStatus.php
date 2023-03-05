<?php

namespace App\Entity\Other\ContactsList;

use App\Repository\Other\ContactsList\ContactsListStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactsListStatusRepository::class)]
class ContactsListStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: ContactsList::class)]
    private Collection $contactsList;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: ContactsListStatusVariants::class)]
    private Collection $variants;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameAction;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorText;

    public function __construct()
    {
        $this->contactsList = new ArrayCollection();
        $this->variants = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getNameAction(): ?string
    {
        return $this->nameAction;
    }

    public function setNameAction(?string $nameAction): self
    {
        $this->nameAction = $nameAction;

        return $this;
    }

    public function getColorText(): ?string
    {
        return $this->colorText;
    }

    public function setColorText(?string $colorText): self
    {
        $this->colorText = $colorText;

        return $this;
    }


    /**
     * @return Collection<int, ContactsListStatusVariants>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(ContactsListStatusVariants $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setStatus($this);
        }

        return $this;
    }

    public function removeVariant(ContactsListStatusVariants $variant): self
    {
        if ($this->variants->removeElement($variant)) {
            // set the owning side to null (unless already changed)
            if ($variant->getStatus() === $this) {
                $variant->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContactsList>
     */
    public function getContactsList(): Collection
    {
        return $this->contactsList;
    }

    public function addCompany(ContactsList $company): self
    {
        if (!$this->contactsList->contains($company)) {
            $this->contactsList->add($company);
            $company->setStatus($this);
        }

        return $this;
    }

    public function removeCompany(ContactsList $company): self
    {
        if ($this->contactsList->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getStatus() === $this) {
                $company->setStatus(null);
            }
        }

        return $this;
    }

}
