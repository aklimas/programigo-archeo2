<?php

namespace App\Entity\Core\Tasks;

use App\Repository\Core\Tasks\TasksStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TasksStatusRepository::class)]
class TasksStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $color;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Tasks::class)]
    private Collection $tasks;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: TasksStatusVariants::class)]
    private Collection $variants;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameAction;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorText;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
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
     * @return Collection<int, Tasks>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Tasks $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setStatus($this);
        }

        return $this;
    }

    public function removeTask(Tasks $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getStatus() === $this) {
                $task->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TasksStatusVariants>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(TasksStatusVariants $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setStatus($this);
        }

        return $this;
    }

    public function removeVariant(TasksStatusVariants $variant): self
    {
        if ($this->variants->removeElement($variant)) {
            // set the owning side to null (unless already changed)
            if ($variant->getStatus() === $this) {
                $variant->setStatus(null);
            }
        }

        return $this;
    }
}
