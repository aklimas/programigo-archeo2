<?php

namespace App\Entity;

use App\Repository\SecretRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretRepository::class)]
class Secret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accessToken = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $refreshToken = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accessTokenExpiry = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getAccessTokenExpiry(): ?string
    {
        return $this->accessTokenExpiry;
    }

    public function setAccessTokenExpiry(?string $accessTokenExpiry): self
    {
        $this->accessTokenExpiry = $accessTokenExpiry;

        return $this;
    }

}
