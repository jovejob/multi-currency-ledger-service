<?php

// src/CommandHandler/CreateTransactionHandler.php
namespace App\CommandHandler;

use App\DTO\CreateTransactionDTO;
use App\Entity\Transaction;
use App\Entity\Ledger;
use App\Repository\LedgerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

// class CreateTransactionHandler
// {
//   private EntityManagerInterface $entityManager;
//   private LedgerRepository $ledgerRepository;

//   public function __construct(EntityManagerInterface $entityManager, LedgerRepository $ledgerRepository)
//   {
//     $this->entityManager = $entityManager;
//     $this->ledgerRepository = $ledgerRepository;
//   }

//   public function handle(CreateTransactionDTO $dto): JsonResponse
//   {
//     // Validate required fields
//     $requiredFields = ['ledgerId', 'type', 'amount', 'currency'];
//     foreach ($requiredFields as $field) {
//       if (empty($dto->{$field})) {
//         return new JsonResponse([
//           'error' => 'Missing required fields: ' . implode(', ', $requiredFields)
//         ], 400); // Return Bad Request if any required field is missing
//       }
//     }

//     // Validate currency format
//     if (!preg_match('/^[A-Z]{3}$/', $dto->currency)) {
//       return new JsonResponse([
//         'error' => 'Currency must be a valid 3-letter ISO code.'
//       ], 400);
//     }

//     // Fetch the Ledger object by its ID
//     $ledger = $this->ledgerRepository->find($dto->ledgerId);
//     if (!$ledger) {
//       return new JsonResponse([
//         'error' => 'Ledger not found.'
//       ], 404);
//     }

//     // Create the new Transaction
//     $transaction = new Transaction();
//     $transaction->setLedger($ledger);
//     $transaction->setTransactionType($dto->type);
//     $transaction->setAmount($dto->amount);
//     $transaction->setCurrency($dto->currency);

//     // Persist the transaction to the database
//     $this->entityManager->persist($transaction);
//     $this->entityManager->flush();

//     // Return the response with the transaction details
//     return new JsonResponse([
//       'transactionId' => $transaction->getId(),
//       'ledgerId' => $transaction->getLedger()->getId(),
//       'transactionType' => $transaction->getTransactionType(),
//       'amount' => $transaction->getAmount(),
//       'currency' => $transaction->getCurrency(),
//       'createdAt' => $transaction->getCreatedAt()->format('Y-m-d H:i:s'),
//     ]);
//   }

//   private function createTransaction(CreateTransactionDTO $dto)
//   {
//     // Example logic to create a transaction
//     // This can include persisting the transaction and other related operations

//     // For now, we're just creating a mock transaction to return
//     $transaction = new \stdClass(); // Mock transaction object
//     $transaction->id = rand(1, 1000);  // Random ID for illustration
//     $transaction->ledger = (object) ['id' => $dto->ledgerId];
//     $transaction->transactionType = $dto->type;
//     $transaction->amount = $dto->amount;

//     return $transaction;
//   }
// }

class CreateTransactionHandler
{
  private EntityManagerInterface $entityManager;
  private LedgerRepository $ledgerRepository;

  public function __construct(EntityManagerInterface $entityManager, LedgerRepository $ledgerRepository)
  {
    $this->entityManager = $entityManager;
    $this->ledgerRepository = $ledgerRepository;
  }

  public function handle(CreateTransactionDTO $dto): JsonResponse
  {
    // Validate required fields
    $requiredFields = ['ledgerId', 'type', 'amount', 'currency'];
    foreach ($requiredFields as $field) {
      if (empty($dto->{$field})) {
        return new JsonResponse([
          'error' => "Missing required field: $field."
        ], 400);
      }
    }

    // Validate currency format (ISO 4217)
    if (!preg_match('/^[A-Z]{3}$/', $dto->currency)) {
      return new JsonResponse([
        'error' => 'Currency must be a valid 3-letter ISO code.'
      ], 400);
    }

    // Fetch the Ledger object by its ID
    $ledger = $this->ledgerRepository->find($dto->ledgerId);
    if (!$ledger) {
      return new JsonResponse([
        'error' => 'Ledger not found.'
      ], 404);
    }

    // Start a transaction to ensure atomicity
    $this->entityManager->beginTransaction();

    try {
      // Create the new Transaction
      $transaction = new Transaction();
      $transaction->setLedger($ledger);
      $transaction->setTransactionType($dto->type);
      $transaction->setAmount($dto->amount);
      $transaction->setCurrency($dto->currency);

      // Update ledger balance
      $this->updateLedgerBalance($ledger, $dto->amount, $dto->currency, $dto->type);

      // Persist changes
      $this->entityManager->persist($transaction);
      $this->entityManager->persist($ledger); // Ensure balance updates are persisted
      $this->entityManager->flush();

      // Commit transaction
      $this->entityManager->commit();

      // Return the response
      return new JsonResponse([
        'transactionId' => $transaction->getId(),
        'ledgerId' => $ledger->getId(),
        'transactionType' => $transaction->getTransactionType(),
        'amount' => $transaction->getAmount(),
        'currency' => $transaction->getCurrency(),
        'createdAt' => $transaction->getCreatedAt()->format('Y-m-d H:i:s'),
      ]);

    } catch (Exception $e) {
      // Rollback transaction on failure
      $this->entityManager->rollback();
      return new JsonResponse([
        'error' => 'Transaction failed: ' . $e->getMessage()
      ], 500);
    }
  }

  private function updateLedgerBalance(Ledger $ledger, float $amount, string $currency, string $transactionType): void
  {
    // Current balance (float) retrieval
    $balance = $ledger->getBalance() ?? 0.0;

    // Ensure the correct balance update for single currency for now (future multi-currency handling will require array)
    // TODO: Implement multi-currency balance handling (balance as array), this will replace float-based balance logic.

    if ($transactionType === 'credit') {
      $balance += $amount;
    } elseif ($transactionType === 'debit') {
      if ($balance < $amount) {
        throw new Exception('Insufficient funds in ledger for this currency.');
      }
      $balance -= $amount;
    } else {
      throw new Exception('Invalid transaction type.');
    }

    // Update the ledger with the new balance (float)
    $ledger->setBalance($balance);
  }

  // todo manage multiple balances
  // separate table ideally or JSON field for storing multiple currency balances
  // jsonb field in Ledger to store currency balances efficiently maybe
  // private function updateLedgerBalance(Ledger $ledger, float $amount, string $currency, string $transactionType): void
  // {
  //   $balances = $ledger->getBalances() ?? []; // Assume Ledger has a getBalances() method returning an array

  //   // Ensure currency entry exists
  //   if (!isset($balances[$currency])) {
  //     $balances[$currency] = 0.0;
  //   }

  //   // Adjust balance
  //   if ($transactionType === 'credit') {
  //     $balances[$currency] += $amount;
  //   } elseif ($transactionType === 'debit') {
  //     if ($balances[$currency] < $amount) {
  //       throw new Exception('Insufficient funds in ledger for this currency.');
  //     }
  //     $balances[$currency] -= $amount;
  //   } else {
  //     throw new Exception('Invalid transaction type.');
  //   }

  //   // Update ledger balances
  //   $ledger->setBalances($balances);
  // }
}
