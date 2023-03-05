<?php

namespace App\Entity\Core\Tasks;

use App\Entity\User;
use App\Repository\Core\Tasks\TasksHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TasksHistoryRepository::class)]
class TasksHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'histories')]
    private ?Tasks $tasks;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasksHistories')]
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

    public function getTasks(): ?Tasks
    {
        return $this->tasks;
    }

    public function setTasks(?Tasks $tasks): self
    {
        $this->tasks = $tasks;

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
