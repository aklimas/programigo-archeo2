<?php

namespace App\Entity\Core;

use App\Entity\Core\Files\Files;
use App\Entity\User;
use App\Repository\Core\UserDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDataRepository::class)]
class UserData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userData', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $lastName;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $phone;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $street;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $postcode;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $city;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $nip;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $company;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $regon;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $homeNumber;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $apartmentNumber;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $social_Facebook;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $social_Twitter;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $social_Instagram;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string  $social_Linkedin;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $social_Youtube;

    #[ORM\OneToOne(inversedBy: 'userData', cascade: ['persist', 'remove'])]
    private ?Files $photo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paycheckProcent = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function setNip(?string $nip): self
    {
        $this->nip = $nip;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getRegon(): ?string
    {
        return $this->regon;
    }

    public function setRegon(?string $regon): self
    {
        $this->regon = $regon;

        return $this;
    }

    public function getHomeNumber(): ?string
    {
        return $this->homeNumber;
    }

    public function setHomeNumber(?string $homeNumber): self
    {
        $this->homeNumber = $homeNumber;

        return $this;
    }

    public function getApartmentNumber(): ?string
    {
        return $this->apartmentNumber;
    }

    public function setApartmentNumber(?string $apartmentNumber): self
    {
        $this->apartmentNumber = $apartmentNumber;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?Files
    {
        return $this->photo;
    }

    public function setPhoto(?Files $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getSocialFacebook(): ?string
    {
        return $this->social_Facebook;
    }

    public function setSocialFacebook(?string $social_Facebook): self
    {
        $this->social_Facebook = $social_Facebook;

        return $this;
    }

    public function getSocialTwitter(): ?string
    {
        return $this->social_Twitter;
    }

    public function setSocialTwitter(?string $social_Twitter): self
    {
        $this->social_Twitter = $social_Twitter;

        return $this;
    }

    public function getSocialInstagram(): ?string
    {
        return $this->social_Instagram;
    }

    public function setSocialInstagram(?string $social_Instagram): self
    {
        $this->social_Instagram = $social_Instagram;

        return $this;
    }

    public function getSocialLinkedin(): ?string
    {
        return $this->social_Linkedin;
    }

    public function setSocialLinkedin(?string $social_Linkedin): self
    {
        $this->social_Linkedin = $social_Linkedin;

        return $this;
    }

    public function getSocialYoutube(): ?string
    {
        return $this->social_Youtube;
    }

    public function setSocialYoutube(?string $social_Youtube): self
    {
        $this->social_Youtube = $social_Youtube;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->name.' '.$this->lastName;
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

    public function getPaycheckProcent(): ?string
    {
        return $this->paycheckProcent;
    }

    public function setPaycheckProcent(?string $paycheckProcent): self
    {

        $paycheckProcent = str_replace(',','.', $paycheckProcent);
        $this->paycheckProcent = $paycheckProcent;

        return $this;
    }
}
