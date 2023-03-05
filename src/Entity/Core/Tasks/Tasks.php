<?php

namespace App\Entity\Core\Tasks;

use App\Entity\Core\Comments;
use App\Entity\Core\Files\Files;
use App\Repository\Core\Tasks\TasksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TasksRepository::class)]
class Tasks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModify;

    #[ORM\ManyToOne(targetEntity: TasksStatus::class, inversedBy: 'tasks')]
    private ?TasksStatus $status = null;

    #[ORM\OneToMany(mappedBy: 'tasks', targetEntity: TasksHistory::class)]
    private Collection $histories;

    #[ORM\OneToMany(mappedBy: 'tasks', targetEntity: Comments::class)]
    private Collection $comments;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commend;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $param;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type;

    #[ORM\ManyToOne(targetEntity: Files::class, inversedBy: 'tasks')]
    private ?Files $file;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $error;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->histories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateModify(): ?\DateTimeInterface
    {
        return $this->dateModify;
    }

    public function setDateModify(?\DateTimeInterface $dateModify): self
    {
        $this->dateModify = $dateModify;

        return $this;
    }

    public function getCommend(): ?string
    {
        return $this->commend;
    }

    public function setCommend(string $commend): self
    {
        $this->commend = $commend;

        return $this;
    }

    public function getParam(): ?string
    {
        return $this->param;
    }

    public function setParam(?string $param): self
    {
        $this->param = $param;

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

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTasks($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTasks() === $this) {
                $comment->setTasks(null);
            }
        }

        return $this;
    }

    public function getFile(): ?Files
    {
        return $this->file;
    }

    public function setFile(?Files $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection<int, TasksHistory>
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(TasksHistory $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories->add($history);
            $history->setTasks($this);
        }

        return $this;
    }

    public function removeHistory(TasksHistory $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getTasks() === $this) {
                $history->setTasks(null);
            }
        }

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
