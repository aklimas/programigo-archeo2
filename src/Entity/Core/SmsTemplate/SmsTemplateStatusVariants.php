<?php

namespace App\Entity\Core\SmsTemplate;

use App\Repository\Core\SmsTemplate\SmsTemplateStatusVariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmsTemplateStatusVariantsRepository::class)]
class SmsTemplateStatusVariants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role;

    #[ORM\Column(length: 255)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: SmsTemplateStatus::class, inversedBy: 'variants')]
    private ?SmsTemplateStatus $status;

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

    public function getStatus(): ?SmsTemplateStatus
    {
        return $this->status;
    }

    public function setStatus(?SmsTemplateStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
