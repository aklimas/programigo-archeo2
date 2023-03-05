<?php

namespace App\Entity\Core\SmsTemplate;

use App\Repository\Core\SmsTemplate\SmsTemplateStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmsTemplateStatusRepository::class)]
class SmsTemplateStatus
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

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: SmsTemplate::class)]
    private Collection $smsTemplates;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: SmsTemplateStatusVariants::class)]
    private Collection $variants;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameAction;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $colorText;

    public function __construct()
    {
        $this->smsTemplates = new ArrayCollection();
        $this->variants = new ArrayCollection();
    }

    public function getId(): ?string
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
     * @return Collection<int, SmsTemplate>
     */
    public function getSmsTemplates(): Collection
    {
        return $this->smsTemplates;
    }

    public function addSmsTemplate(SmsTemplate $smsTemplate): self
    {
        if (!$this->smsTemplates->contains($smsTemplate)) {
            $this->smsTemplates->add($smsTemplate);
            $smsTemplate->setStatus($this);
        }

        return $this;
    }

    public function removeSmsTemplate(SmsTemplate $smsTemplate): self
    {
        if ($this->smsTemplates->removeElement($smsTemplate)) {
            // set the owning side to null (unless already changed)
            if ($smsTemplate->getStatus() === $this) {
                $smsTemplate->setStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SmsTemplateStatusVariants>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(SmsTemplateStatusVariants $variant): self
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setStatus($this);
        }

        return $this;
    }

    public function removeVariant(SmsTemplateStatusVariants $variant): self
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
