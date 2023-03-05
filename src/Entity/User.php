<?php

namespace App\Entity;

use App\Entity\Core\Comments;
use App\Entity\Core\EmailQueue;
use App\Entity\Core\Messenger\Messenger;
use App\Entity\Core\Tasks\TasksHistory;
use App\Entity\Core\UserData;
use App\Entity\Core\UserEmail;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserData $userData = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserEmail $userEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmed;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $actived = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $access;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateRegister = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateActivity = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $apiToken;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comments::class)]
    private Collection $commentsAuthor;

    #[ORM\OneToMany(mappedBy: 'userRead', targetEntity: Comments::class)]
    private Collection $commentsRead;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EmailQueue::class)]
    private Collection $emailQueues;


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TasksHistory::class)]
    private Collection $tasksHistories;


    #[ORM\OneToMany(mappedBy: 'userSend', targetEntity: Messenger::class)]
    private Collection $messengersSend;


    #[ORM\OneToMany(mappedBy: 'userReceipt', targetEntity: Messenger::class)]
    private Collection $messengersReceipt;



    public function __construct()
    {
        $this->commentsAuthor = new ArrayCollection();
        $this->commentsRead = new ArrayCollection();
        $this->emailQueues = new ArrayCollection();
        $this->messengersSend = new ArrayCollection();
        $this->messengersReceipt = new ArrayCollection();
        $this->tasksHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function getUserData(): ?UserData
    {
        return $this->userData;
    }

    public function setUserData(?UserData $userData): self
    {
        // unset the owning side of the relation if necessary
        if (null === $userData && null !== $this->userData) {
            $this->userData->setUser(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $userData && $userData->getUser() !== $this) {
            $userData->setUser($this);
        }

        $this->userData = $userData;

        return $this;
    }

    public function getUserEmail(): ?UserEmail
    {
        return $this->userEmail;
    }

    public function setUserEmail(?UserEmail $userEmail): self
    {
        // unset the owning side of the relation if necessary
        if (null === $userEmail && null !== $this->userEmail) {
            $this->userEmail->setUser(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $userEmail && $userEmail->getUser() !== $this) {
            $userEmail->setUser($this);
        }

        $this->userEmail = $userEmail;

        return $this;
    }

    public function getConfirmed(): ?string
    {
        return $this->confirmed;
    }

    public function setConfirmed(?string $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function isActived(): ?bool
    {
        return $this->actived;
    }

    public function setActived(?bool $actived): self
    {
        $this->actived = $actived;

        return $this;
    }

    public function isAccess(): ?bool
    {
        return $this->access;
    }

    public function setAccess(?bool $access): self
    {
        $this->access = $access;

        return $this;
    }

    public function getDateRegister(): ?\DateTimeInterface
    {
        return $this->dateRegister;
    }

    public function setDateRegister(?\DateTimeInterface $dateRegister): self
    {
        $this->dateRegister = $dateRegister;

        return $this;
    }

    public function getDateActivity(): ?\DateTimeInterface
    {
        return $this->dateActivity;
    }

    public function setDateActivity(?\DateTimeInterface $dateActivity): self
    {
        $this->dateActivity = $dateActivity;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }



    /**
     * @return Collection<int, EmailQueue>
     */
    public function getEmailQueues(): Collection
    {
        return $this->emailQueues;
    }

    public function addEmailQueue(EmailQueue $emailQueue): self
    {
        if (!$this->emailQueues->contains($emailQueue)) {
            $this->emailQueues->add($emailQueue);
            $emailQueue->setUser($this);
        }

        return $this;
    }

    public function removeEmailQueue(EmailQueue $emailQueue): self
    {
        if ($this->emailQueues->removeElement($emailQueue)) {
            // set the owning side to null (unless already changed)
            if ($emailQueue->getUser() === $this) {
                $emailQueue->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messenger>
     */
    public function getMessengersSend(): Collection
    {
        return $this->messengersSend;
    }

    public function addMessengersSend(Messenger $messengersSend): self
    {
        if (!$this->messengersSend->contains($messengersSend)) {
            $this->messengersSend->add($messengersSend);
            $messengersSend->setUserSend($this);
        }

        return $this;
    }

    public function removeMessengersSend(Messenger $messengersSend): self
    {
        if ($this->messengersSend->removeElement($messengersSend)) {
            // set the owning side to null (unless already changed)
            if ($messengersSend->getUserSend() === $this) {
                $messengersSend->setUserSend(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messenger>
     */
    public function getMessengersReceipt(): Collection
    {
        return $this->messengersReceipt;
    }

    public function addMessengersReceipt(Messenger $messengersReceipt): self
    {
        if (!$this->messengersReceipt->contains($messengersReceipt)) {
            $this->messengersReceipt->add($messengersReceipt);
            $messengersReceipt->setUserReceipt($this);
        }

        return $this;
    }

    public function removeMessengersReceipt(Messenger $messengersReceipt): self
    {
        if ($this->messengersReceipt->removeElement($messengersReceipt)) {
            // set the owning side to null (unless already changed)
            if ($messengersReceipt->getUserReceipt() === $this) {
                $messengersReceipt->setUserReceipt(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TasksHistory>
     */
    public function getTasksHistories(): Collection
    {
        return $this->tasksHistories;
    }

    public function addTasksHistory(TasksHistory $tasksHistory): self
    {
        if (!$this->tasksHistories->contains($tasksHistory)) {
            $this->tasksHistories->add($tasksHistory);
            $tasksHistory->setUser($this);
        }

        return $this;
    }

    public function removeTasksHistory(TasksHistory $tasksHistory): self
    {
        if ($this->tasksHistories->removeElement($tasksHistory)) {
            // set the owning side to null (unless already changed)
            if ($tasksHistory->getUser() === $this) {
                $tasksHistory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getCommentsAuthor(): Collection
    {
        return $this->commentsAuthor;
    }

    public function addCommentsAuthor(Comments $commentsAuthor): self
    {
        if (!$this->commentsAuthor->contains($commentsAuthor)) {
            $this->commentsAuthor->add($commentsAuthor);
            $commentsAuthor->setAuthor($this);
        }

        return $this;
    }

    public function removeCommentsAuthor(Comments $commentsAuthor): self
    {
        if ($this->commentsAuthor->removeElement($commentsAuthor)) {
            // set the owning side to null (unless already changed)
            if ($commentsAuthor->getAuthor() === $this) {
                $commentsAuthor->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getCommentsRead(): Collection
    {
        return $this->commentsRead;
    }

    public function addCommentsRead(Comments $commentsRead): self
    {
        if (!$this->commentsRead->contains($commentsRead)) {
            $this->commentsRead->add($commentsRead);
            $commentsRead->setUserRead($this);
        }

        return $this;
    }

    public function removeCommentsRead(Comments $commentsRead): self
    {
        if ($this->commentsRead->removeElement($commentsRead)) {
            // set the owning side to null (unless already changed)
            if ($commentsRead->getUserRead() === $this) {
                $commentsRead->setUserRead(null);
            }
        }

        return $this;
    }

}
