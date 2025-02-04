<?php

// src/CommandHandler/CreateTransactionHandler.php
namespace App\CommandHandler;

use App\DTO\CreateTransactionDTO;
use App\Entity\Transaction;
use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;

class CreateTransactionHandler
{
  private EntityManagerInterface $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function handle(CreateTransactionDTO $dto): Transaction
  {
    $ledger = $this->em->getRepository(Ledger::class)->find($dto->ledgerId);
    if (!$ledger) {
      throw new \Exception('Ledger not found');
    }

    $transaction = new Transaction();
    $transaction->setLedger($ledger);
    $transaction->setTransactionType($dto->type);
    $transaction->setAmount($dto->amount);
    $transaction->setCurrency($dto->currency);
    $transaction->setCreatedAt(new \DateTime());

    // Update Ledger balance accordingly
    if ($dto->type === 'credit') {
      $ledger->setBalance($ledger->getBalance() + $dto->amount);
    } else {
      $ledger->setBalance($ledger->getBalance() - $dto->amount);
    }

    $this->em->persist($transaction);
    $this->em->persist($ledger);
    $this->em->flush();

    return $transaction;
  }
}
