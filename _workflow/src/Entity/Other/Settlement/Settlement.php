<?php

namespace App\Entity\Other\Settlement;

use App\Entity\Core\Comments;
use App\Entity\Core\Files\Files;
use App\Entity\Other\Settlement\SettlementHistory;
use App\Repository\Other\Settlement\SettlementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettlementRepository::class)]
class Settlement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAdd = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateModify;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content;

    // TODO Statusy
    //#[ORM\ManyToOne(targetEntity: SettlementStatus::class, inversedBy: 'settlement')]
    //private ?SettlementStatus $status;

    // TODO Komentarze
    //#[ORM\OneToMany(mappedBy: 'settlement', targetEntity: Comments::class)]
    //private Collection $comments;

    // TODO Historia
    //#[ORM\OneToMany(mappedBy: 'settlement', targetEntity: SettlementHistory::class)]
    //private Collection $histories;

    // TODO Pliki
    //#[ORM\OneToMany(mappedBy: 'settlement', targetEntity: Files::class)]
    //private Collection $file;

    // TODO Dodać do DataFitures SQL
    // INSERT INTO `settlement_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`) VALUES(1, 'Aktywna', 'enabled', '#5A0', NULL, NULL);

    // TODO Komentarze - dodać do entity Comments
    // #[ORM\ManyToOne(targetEntity: Settlement::class, inversedBy: 'comments')]
    // private ?Settlement $settlement;

    // TODO Pliki - dodać do entity Files
    //#[ORM\ManyToOne(inversedBy: 'file')]
    //private ?Settlement $settlement = null;

    // TODO Historia- dodać do entity User
    //#[ORM\OneToMany(mappedBy: 'user', targetEntity: SettlementHistory::class)]
    //private Collection $settlementHistories;


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

    public function setDateAdd(\DateTimeInterface $dateAdd): self
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


}
