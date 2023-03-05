<?php

namespace App\Entity\Core\SmsTemplate;

use App\Repository\Core\SmsTemplate\SmsTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmsTemplateRepository::class)]
class SmsTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModify;

    #[ORM\ManyToOne(targetEntity: SmsTemplateStatus::class, inversedBy: 'smsTemplates')]
    private ?SmsTemplateStatus $status;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(?\DateTimeInterface $dateAdd): self
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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
