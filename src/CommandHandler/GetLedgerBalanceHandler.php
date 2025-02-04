<?php

// src/CommandHandler/GetLedgerBalanceHandler.php
namespace App\CommandHandler;

use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;


class GetLedgerBalanceHandler
{
  private EntityManagerInterface $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function handle(int $ledgerId): ?Ledger
  {
    return $this->em->getRepository(Ledger::class)->find($ledgerId);
  }
}
