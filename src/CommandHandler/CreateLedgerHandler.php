<?php

// src/CommandHandler/CreateLedgerHandler.php
namespace App\CommandHandler;

use App\DTO\CreateLedgerDTO;
use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;

class CreateLedgerHandler
{
  private EntityManagerInterface $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function handle(CreateLedgerDTO $dto): Ledger
  {
    // Create and persist Ledger entity
    $ledger = new Ledger();
    $ledger->setName($dto->name);
    $ledger->setCurrency($dto->currency);
    $this->em->persist($ledger);
    $this->em->flush();

    return $ledger;
  }
}
