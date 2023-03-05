<?php

namespace App\Entity\Core\Files;

use App\Entity\Core\Comments;
use App\Entity\Core\Settings;
use App\Entity\Core\Tasks\Tasks;
use App\Entity\Core\UserData;
use App\Repository\Core\Files\FilesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilesRepository::class)]
class Files
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $extension;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModify = null;

    #[ORM\OneToOne(mappedBy: 'photo', cascade: ['persist'])]
    private ?UserData $userData = null;

    #[ORM\OneToOne(mappedBy: 'file', cascade: ['persist', 'remove'])]
    private ?Settings $settings = null;

    #[ORM\ManyToOne(targetEntity: FilesStatus::class, inversedBy: 'files')]
    private ?\App\Entity\Core\Files\FilesStatus $status;

    #[ORM\OneToMany(mappedBy: 'files', targetEntity: Comments::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'files', targetEntity: FilesHistory::class)]
    private Collection $histories;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: Tasks::class)]
    private Collection $tasks;



    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getFullPath()
    {
        return $this->getPath().$this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
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

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

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

    public function getUserData(): ?UserData
    {
        return $this->userData;
    }

    public function setUserData(?UserData $userData): self
    {
        // unset the owning side of the relation if necessary
        if (null === $userData && null !== $this->userData) {
            $this->userData->setPhoto(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $userData && $userData->getPhoto() !== $this) {
            $userData->setPhoto($this);
        }

        $this->userData = $userData;

        return $this;
    }

    public function getSettings(): ?Settings
    {
        return $this->settings;
    }

    public function setSettings(?Settings $settings): self
    {
        // unset the owning side of the relation if necessary
        if (null === $settings && null !== $this->settings) {
            $this->settings->setFile(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $settings && $settings->getFile() !== $this) {
            $settings->setFile($this);
        }

        $this->settings = $settings;

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
            $comment->setFiles($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFiles() === $this) {
                $comment->setFiles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FilesHistory>
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(FilesHistory $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories->add($history);
            $history->setFiles($this);
        }

        return $this;
    }

    public function removeHistory(FilesHistory $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getFiles() === $this) {
                $history->setFiles(null);
            }
        }

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
            $task->setFile($this);
        }

        return $this;
    }

    public function removeTask(Tasks $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getFile() === $this) {
                $task->setFile(null);
            }
        }

        return $this;
    }

}
