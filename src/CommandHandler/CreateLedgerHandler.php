<?php

// src/CommandHandler/CreateLedgerHandler.php
namespace App\CommandHandler;

use App\DTO\CreateLedgerDTO;
use App\Service\LedgerService;
use App\Entity\Ledger;

class CreateLedgerHandler
{
  private LedgerService $ledgerService;

  public function __construct(LedgerService $ledgerService)
  {
    $this->ledgerService = $ledgerService;
  }

  public function handle(CreateLedgerDTO $dto): Ledger
  {
    return $this->ledgerService->createLedger($dto->name, $dto->currency);
  }
}
