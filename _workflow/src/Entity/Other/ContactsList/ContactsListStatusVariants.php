<?php

namespace App\Entity\Other\ContactsList;

use App\Repository\Other\ContactsList\ContactsListStatusVariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactsListStatusVariantsRepository::class)]
class ContactsListStatusVariants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role;

    #[ORM\Column(length: 255)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: ContactsListStatus::class, inversedBy: 'variants')]
    private ?ContactsListStatus $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?ContactsListStatus
    {
        return $this->status;
    }

    public function setStatus(?ContactsListStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
