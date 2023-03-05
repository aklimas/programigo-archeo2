<?php

namespace App\Entity\Core\Files;

use App\Repository\Core\Files\FilesHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilesHistoryRepository::class)]
class FilesHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Files::class, inversedBy: 'histories')]
    private ?Files $files;

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
