<?php

namespace App\Entity\Core\Files;

use App\Repository\Core\Files\FilesStatusVariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilesStatusVariantsRepository::class)]
class FilesStatusVariants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role;

    #[ORM\Column(length: 255)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: FilesStatus::class, inversedBy: 'variants')]
    private ?FilesStatus $status;

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

    public function getStatus(): ?FilesStatus
    {
        return $this->status;
    }

    public function setStatus(?FilesStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
