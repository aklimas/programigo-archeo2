<?php

namespace App\Entity\Other\Settlement;

use App\Repository\Other\Settlement\SettlementStatusVariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettlementStatusVariantsRepository::class)]
class SettlementStatusVariants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role;

    #[ORM\Column(length: 255)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: SettlementStatus::class, inversedBy: 'variants')]
    private ?SettlementStatus $status;

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

    public function getStatus(): ?SettlementStatus
    {
        return $this->status;
    }

    public function setStatus(?SettlementStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
