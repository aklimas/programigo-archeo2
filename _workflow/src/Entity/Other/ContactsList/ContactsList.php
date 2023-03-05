<?php

namespace App\Entity\Other\ContactsList;

use App\Entity\Core\Comments;
use App\Entity\Core\Files\Files;
use App\Entity\Other\ContactsList\ContactsListHistory;
use App\Repository\Other\ContactsList\ContactsListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactsListRepository::class)]
class ContactsList
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
    //#[ORM\ManyToOne(targetEntity: ContactsListStatus::class, inversedBy: 'contactsList')]
    //private ?ContactsListStatus $status;

    // TODO Komentarze
    //#[ORM\OneToMany(mappedBy: 'contactsList', targetEntity: Comments::class)]
    //private Collection $comments;

    // TODO Historia
    //#[ORM\OneToMany(mappedBy: 'contactsList', targetEntity: ContactsListHistory::class)]
    //private Collection $histories;

    // TODO Pliki
    //#[ORM\OneToMany(mappedBy: 'contactsList', targetEntity: Files::class)]
    //private Collection $file;

    // TODO Dodać do DataFitures SQL
    // INSERT INTO `contactsList_status` (`id`, `name`, `value`, `color`, `name_action`, `color_text`) VALUES(1, 'Aktywna', 'enabled', '#5A0', NULL, NULL);

    // TODO Komentarze - dodać do entity Comments
    // #[ORM\ManyToOne(targetEntity: ContactsList::class, inversedBy: 'comments')]
    // private ?ContactsList $contactsList;

    // TODO Pliki - dodać do entity Files
    //#[ORM\ManyToOne(inversedBy: 'file')]
    //private ?ContactsList $contactsList = null;

    // TODO Historia- dodać do entity User
    //#[ORM\OneToMany(mappedBy: 'user', targetEntity: ContactsListHistory::class)]
    //private Collection $contactsListHistories;


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
