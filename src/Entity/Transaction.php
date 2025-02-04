<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column(type: "integer")]
  private int $id;

  #[ORM\ManyToOne(targetEntity: Ledger::class, inversedBy: 'transaction')]
  #[ORM\JoinColumn(name: "ledger_id", referencedColumnName: "id")]
  private Ledger $ledger;

  #[ORM\Column(type: "string")]
  private string $transactionType; // debit or credit

  #[ORM\Column(type: "float")]
  private float $amount;

  #[ORM\Column(type: "string", length: 3)]
  private string $currency;

  #[ORM\Column(type: "datetime")]
  private \DateTime $createdAt;

  public function __construct()
  {
    $this->createdAt = new \DateTime();
  }

  // Getters and Setters...
  public function getId(): ?int
  {
    return $this->id;
  }

  public function getLedger(): Ledger
  {
    return $this->ledger;
  }

  public function setLedger(Ledger $ledger): self
  {
    $this->ledger = $ledger;
    return $this;
  }

  public function getTransactionType(): string
  {
    return $this->transactionType;
  }

  public function setTransactionType(string $transactionType): self
  {
    if (!in_array($transactionType, ['debit', 'credit'])) {
      throw new \InvalidArgumentException("Transaction type must be either 'debit' or 'credit'.");
    }
    $this->transactionType = $transactionType;
    return $this;
  }

  public function getAmount(): float
  {
    return $this->amount;
  }

  public function setAmount(float $amount): self
  {
    if ($amount <= 0) {
      throw new \InvalidArgumentException("Amount must be greater than zero.");
    }
    $this->amount = $amount;
    return $this;
  }

  public function getCurrency(): string
  {
    return $this->currency;
  }

  public function setCurrency(string $currency): self
  {
    if (strlen($currency) !== 3) {
      throw new \InvalidArgumentException("Currency must be a valid 3-letter ISO code.");
    }
    $this->currency = strtoupper($currency);
    return $this;
  }

  public function getCreatedAt(): \DateTime
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTime $createdAt): self
  {
    $this->createdAt = $createdAt;
    return $this;
  }
}
