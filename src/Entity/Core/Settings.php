<?php

namespace App\Entity\Core;

use App\Entity\Core\Files\Files;
use App\Repository\Core\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $value = null;

    #[ORM\OneToOne(inversedBy: 'settings', cascade: ['persist', 'remove'])]
    private ?Files $file = null;

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

    public function setValue(string $value): self
    {
        $this->value = $value;

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
}
