<?php

namespace App\Entity\Core\Files;

use App\Repository\Core\Files\FilesStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilesStatusRepository::class)]
class FilesStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Files::class)]
    private ?Collection $files;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: FilesStatusVariants::class)]
    private ?Collection $variants;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameAction;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorText;

    public function __construct()
    {
        $this->files = new ArrayCollection();
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
     * @return Collection<int, Files>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(Files $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setStatus($this);
        }

        return $this;
    }

    public function removeFile(Files $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getStatus() === $this) {
                $file->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FilesStatusVariants>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(FilesStatusVariants $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setStatus($this);
        }

        return $this;
    }

    public function removeVariant(FilesStatusVariants $variant): self
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
