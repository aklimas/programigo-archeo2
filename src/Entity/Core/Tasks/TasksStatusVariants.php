<?php

namespace App\Entity\Core\Tasks;

use App\Repository\Core\Tasks\TasksStatusVariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TasksStatusVariantsRepository::class)]
class TasksStatusVariants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role;

    #[ORM\Column(length: 255)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: TasksStatus::class, inversedBy: 'variants')]
    private ?TasksStatus $status;

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

    public function getStatus(): ?TasksStatus
    {
        return $this->status;
    }

    public function setStatus(?TasksStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
