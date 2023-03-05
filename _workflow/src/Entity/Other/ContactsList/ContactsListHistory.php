<?php

namespace App\Entity\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsList;
use App\Entity\User;
use App\Repository\Other\ContactsList\ContactsListHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactsListHistoryRepository::class)]
class ContactsListHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ContactsList::class, inversedBy: 'histories')]
    private ?ContactsList $contactsList;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'contactsListHistories')]
    private ?User $user;



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date;

    #[ORM\Column(length: 255)]
    private ?string $value;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getContactsList(): ?ContactsList
    {
        return $this->contactsList;
    }

    public function setContactsList(?ContactsList $contactsList): self
    {
        $this->contactsList = $contactsList;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
