<?php

namespace App\Entity\Core\EmailTemplate;

use App\Repository\Core\EmailTemplate\EmailTemplateStatusVariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailTemplateStatusVariantsRepository::class)]
class EmailTemplateStatusVariants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role;

    #[ORM\Column(length: 255)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: EmailTemplateStatus::class, inversedBy: 'variants')]
    private ?EmailTemplateStatus $status;

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

    public function getStatus(): ?EmailTemplateStatus
    {
        return $this->status;
    }

    public function setStatus(?EmailTemplateStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
