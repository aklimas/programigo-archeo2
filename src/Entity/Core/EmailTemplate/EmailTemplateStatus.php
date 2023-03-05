<?php

namespace App\Entity\Core\EmailTemplate;

use App\Repository\Core\EmailTemplate\EmailTemplateStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailTemplateStatusRepository::class)]
class EmailTemplateStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $color;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: EmailTemplate::class)]
    private Collection $emailTemplates;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: EmailTemplateStatusVariants::class)]
    private Collection $variants;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameAction;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorText;

    public function __construct()
    {
        $this->emailTemplates = new ArrayCollection();
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

    /**
     * @return Collection<int, EmailTemplate>
     */
    public function getEmailTemplates(): Collection
    {
        return $this->emailTemplates;
    }

    public function addEmailTemplate(EmailTemplate $emailTemplate): self
    {
        if (!$this->emailTemplates->contains($emailTemplate)) {
            $this->emailTemplates[] = $emailTemplate;
            $emailTemplate->setStatus($this);
        }

        return $this;
    }

    public function removeEmailTemplate(EmailTemplate $emailTemplate): self
    {
        if ($this->emailTemplates->removeElement($emailTemplate)) {
            // set the owning side to null (unless already changed)
            if ($emailTemplate->getStatus() === $this) {
                $emailTemplate->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EmailTemplateStatusVariants>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(EmailTemplateStatusVariants $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants[] = $variant;
            $variant->setStatus($this);
        }

        return $this;
    }

    public function removeVariant(EmailTemplateStatusVariants $variant): self
    {
        if ($this->variants->removeElement($variant)) {
            // set the owning side to null (unless already changed)
            if ($variant->getStatus() === $this) {
                $variant->setStatus(null);
            }
        }

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

    public function getNameAction(): ?string
    {
        return $this->nameAction;
    }

    public function setNameAction(?string $nameAction): self
    {
        $this->nameAction = $nameAction;

        return $this;
    }
}
