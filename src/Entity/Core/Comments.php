<?php

namespace App\Entity\Core;

use App\Entity\Core\Files\Files;
use App\Entity\Core\Tasks\Tasks;
use App\Entity\User;
use App\Repository\Core\CommentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $postedDate;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment;

    #[ORM\ManyToOne(targetEntity: Comments::class, inversedBy: 'parents')]
    private ?Comments $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Comments::class)]
    private ?Collection $parents;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commentsAuthor')]
    private ?User $author;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commentsRead')]
    private ?User $userRead;

    #[ORM\ManyToOne(targetEntity: Tasks::class, inversedBy: 'comments')]
    private ?Tasks $tasks;

    #[ORM\ManyToOne(targetEntity: Files::class, inversedBy: 'comments')]
    private ?Files $files = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private $isPublic;

    public function __construct()
    {
        $this->parents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostedDate(): ?\DateTimeInterface
    {
        return $this->postedDate;
    }

    public function setPostedDate(?\DateTimeInterface $postedDate): self
    {
        $this->postedDate = $postedDate;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function isIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(?bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(Comments $parent): self
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
            $parent->setParent($this);
        }

        return $this;
    }

    public function removeParent(Comments $parent): self
    {
        if ($this->parents->removeElement($parent)) {
            // set the owning side to null (unless already changed)
            if ($parent->getParent() === $this) {
                $parent->setParent(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getUserRead(): ?User
    {
        return $this->userRead;
    }

    public function setUserRead(?User $userRead): self
    {
        $this->userRead = $userRead;

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

    public function getFiles(): ?Files
    {
        return $this->files;
    }

    public function setFiles(?Files $files): self
    {
        $this->files = $files;

        return $this;
    }


}
