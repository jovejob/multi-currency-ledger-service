<?php

// src/CommandHandler/CreateTransactionHandler.php
namespace App\CommandHandler;

use App\DTO\CreateTransactionDTO;
use App\Entity\Transaction;
use App\Entity\Ledger;
use App\Repository\LedgerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateTransactionHandler
{
  // private EntityManagerInterface $em;

  private EntityManagerInterface $entityManager;
  private LedgerRepository $ledgerRepository;

  // public function __construct(EntityManagerInterface $em)
  // {
  //   $this->em = $em;
  // }

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
          'error' => 'Missing required fields: ' . implode(', ', $requiredFields)
        ], 400); // Return Bad Request if any required field is missing
      }
    }

    // Validate currency format
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

    // Create the new Transaction
    $transaction = new Transaction();
    $transaction->setLedger($ledger);
    $transaction->setTransactionType($dto->type);
    $transaction->setAmount($dto->amount);
    $transaction->setCurrency($dto->currency);

    // Persist the transaction to the database
    $this->entityManager->persist($transaction);
    $this->entityManager->flush();

    // Return the response with the transaction details
    return new JsonResponse([
      'transactionId' => $transaction->getId(),
      'ledgerId' => $transaction->getLedger()->getId(),
      'transactionType' => $transaction->getTransactionType(),
      'amount' => $transaction->getAmount(),
      'currency' => $transaction->getCurrency(),
      'createdAt' => $transaction->getCreatedAt()->format('Y-m-d H:i:s'),
    ]);
  }

  private function createTransaction(CreateTransactionDTO $dto)
  {
    // Example logic to create a transaction
    // This can include persisting the transaction and other related operations

    // For now, we're just creating a mock transaction to return
    $transaction = new \stdClass(); // Mock transaction object
    $transaction->id = rand(1, 1000);  // Random ID for illustration
    $transaction->ledger = (object) ['id' => $dto->ledgerId];
    $transaction->transactionType = $dto->type;
    $transaction->amount = $dto->amount;

    return $transaction;
  }

  // public function handle(CreateTransactionDTO $dto): Transaction
  // {
  //   $ledger = $this->em->getRepository(Ledger::class)->find($dto->ledgerId);
  //   if (!$ledger) {
  //     throw new \Exception('Ledger not found');
  //   }

  //   $transaction = new Transaction();
  //   $transaction->setLedger($ledger);
  //   $transaction->setTransactionType($dto->type);
  //   $transaction->setAmount($dto->amount);
  //   $transaction->setCurrency($dto->currency);
  //   $transaction->setCreatedAt(new \DateTime());

  //   // Update Ledger balance accordingly
  //   if ($dto->type === 'credit') {
  //     $ledger->setBalance($ledger->getBalance() + $dto->amount);
  //   } else {
  //     $ledger->setBalance($ledger->getBalance() - $dto->amount);
  //   }

  //   $this->em->persist($transaction);
  //   $this->em->persist($ledger);
  //   $this->em->flush();

  //   return $transaction;
  // }
}
