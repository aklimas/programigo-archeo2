<?php

namespace App\Entity\Core;

use App\Entity\User;
use App\Repository\Core\UserEmailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserEmailRepository::class)]
class UserEmail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userEmail', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $senderEmail;

    #[ORM\Column(length: 255)]
    private ?string $senderLabel;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $senderPass;

    #[ORM\Column(length: 255)]
    private ?string $senderHost;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $senderPort;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $footer;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSenderEmail(): ?string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(string $senderEmail): self
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    public function getSenderLabel(): ?string
    {
        return $this->senderLabel;
    }

    public function setSenderLabel(string $senderLabel): self
    {
        $this->senderLabel = $senderLabel;

        return $this;
    }

    public function getSenderPass(): ?string
    {
        return $this->senderPass;
    }

    public function setSenderPass(?string $senderPass): self
    {
        $this->senderPass = $senderPass;

        return $this;
    }

    public function getSenderHost(): ?string
    {
        return $this->senderHost;
    }

    public function setSenderHost(string $senderHost): self
    {
        $this->senderHost = $senderHost;

        return $this;
    }

    public function getSenderPort(): ?string
    {
        return $this->senderPort;
    }

    public function setSenderPort(?string $senderPort): self
    {
        $this->senderPort = $senderPort;

        return $this;
    }

    public function getFooter(): ?string
    {
        return $this->footer;
    }

    public function setFooter(?string $footer): self
    {
        $this->footer = $footer;

        return $this;
    }
}
