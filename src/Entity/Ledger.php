<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "ledgers")]
class Ledger
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  private int $id;

  #[ORM\Column(type: "string", length: 3)]
  private string $currency;

  #[ORM\Column(type: "datetime")]
  private \DateTime $createdAt;

  #[ORM\Column(type: "float", nullable: true)]
  private ?float $balance = 0.0;

  #[ORM\Column(type: "string")]
  private string $name;

  public function __construct()
  {
    $this->createdAt = new \DateTime();
  }

  // Getters and Setters...
  public function getId(): ?int
  {
    return $this->id;
  }

  public function getCurrency(): ?string
  {
    return $this->currency;
  }

  public function setCurrency(string $currency): static
  {
    $this->currency = $currency;
    return $this;
  }

  public function getCreatedAt(): ?\DateTime
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTime $createdAt): static
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  public function getBalance(): ?float
  {
    return $this->balance;
  }

  public function setBalance(float $balance): static
  {
    $this->balance = $balance;
    return $this;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;
    return $this;
  }
}
