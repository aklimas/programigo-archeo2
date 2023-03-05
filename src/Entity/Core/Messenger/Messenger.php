<?php

namespace App\Entity\Core\Messenger;

use App\Entity\User;
use App\Repository\Core\Messenger\MessengerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessengerRepository::class)]
class Messenger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSend;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateReceipt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messengersSend')]
    private ?User $userSend;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messengersReceipt')]
    private ?User $userReceipt;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private $response = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDateSend(): ?\DateTimeInterface
    {
        return $this->dateSend;
    }

    public function setDateSend(\DateTimeInterface $dateSend): self
    {
        $this->dateSend = $dateSend;

        return $this;
    }

    public function getDateReceipt(): ?\DateTimeInterface
    {
        return $this->dateReceipt;
    }

    public function setDateReceipt(?\DateTimeInterface $dateReceipt): self
    {
        $this->dateReceipt = $dateReceipt;

        return $this;
    }

    public function isResponse(): ?bool
    {
        return $this->response;
    }

    public function setResponse(bool $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getUserSend(): ?User
    {
        return $this->userSend;
    }

    public function setUserSend(?User $userSend): self
    {
        $this->userSend = $userSend;

        return $this;
    }

    public function getUserReceipt(): ?User
    {
        return $this->userReceipt;
    }

    public function setUserReceipt(?User $userReceipt): self
    {
        $this->userReceipt = $userReceipt;

        return $this;
    }
}
