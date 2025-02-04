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
    // $balance = $dto->balance ?? 0.0; // Ensure it's always a float
    return $this->ledgerService->createLedger($dto->name, $dto->currency, $dto->balance);
  }
}
