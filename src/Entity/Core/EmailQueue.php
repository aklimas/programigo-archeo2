<?php

namespace App\Entity\Core;

use App\Entity\User;
use App\Repository\Core\EmailQueueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailQueueRepository::class)]
class EmailQueue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private $subject;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private $content;

    #[ORM\Column(length: 255, nullable: true)]
    private $senderEmail;

    #[ORM\Column(length: 255)]
    private $recipient;

    #[ORM\Column(length: 255, nullable: true)]
    private $sender_label;

    #[ORM\Column(length: 255)]
    private $recipient_label;

    #[ORM\Column(type: 'boolean', length: 255, nullable: true)]
    private $readyToShip;

    #[ORM\Column(length: 255)]
    private $status;

    #[ORM\Column(length: 255)]
    private $type;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private $template;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $postDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $createDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $readDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $postPlanDate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'emailQueues')]
    private $user;


    public function __construct()
    {
        $this->status = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
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

    public function getSenderEmail(): ?string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(?string $senderEmail): self
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getSenderLabel(): ?string
    {
        return $this->sender_label;
    }

    public function setSenderLabel(?string $sender_label): self
    {
        $this->sender_label = $sender_label;

        return $this;
    }

    public function getRecipientLabel(): ?string
    {
        return $this->recipient_label;
    }

    public function setRecipientLabel(string $recipient_label): self
    {
        $this->recipient_label = $recipient_label;

        return $this;
    }

    public function isReadyToShip(): ?bool
    {
        return $this->readyToShip;
    }

    public function setReadyToShip(?bool $readyToShip): self
    {
        $this->readyToShip = $readyToShip;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getPostDate(): ?\DateTimeInterface
    {
        return $this->postDate;
    }

    public function setPostDate(?\DateTimeInterface $postDate): self
    {
        $this->postDate = $postDate;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getReadDate(): ?\DateTimeInterface
    {
        return $this->readDate;
    }

    public function setReadDate(?\DateTimeInterface $readDate): self
    {
        $this->readDate = $readDate;

        return $this;
    }

    public function getPostPlanDate(): ?\DateTimeInterface
    {
        return $this->postPlanDate;
    }

    public function setPostPlanDate(?\DateTimeInterface $postPlanDate): self
    {
        $this->postPlanDate = $postPlanDate;

        return $this;
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

}
