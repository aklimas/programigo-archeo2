<?php

namespace App\Entity\Other\Settlement;

use App\Entity\Other\Settlement\Settlement;
use App\Entity\User;
use App\Repository\Other\Settlement\SettlementHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettlementHistoryRepository::class)]
class SettlementHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Settlement::class, inversedBy: 'histories')]
    private ?Settlement $settlement;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'settlementHistories')]
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

    public function getSettlement(): ?Settlement
    {
        return $this->settlement;
    }

    public function setSettlement(?Settlement $settlement): self
    {
        $this->settlement = $settlement;

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
