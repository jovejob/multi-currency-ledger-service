<?php

// src/Controller/TransactionController.php
namespace App\Controller;

use App\DTO\CreateTransactionDTO;
use App\CommandHandler\CreateTransactionHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TransactionController
{
  private CreateTransactionHandler $createTransactionHandler;

  public function __construct(CreateTransactionHandler $createTransactionHandler)
  {
    $this->createTransactionHandler = $createTransactionHandler;
  }

  #[Route('/transactions', methods: ['POST'], name: 'create_transaction')]
  public function createTransaction(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $dto = new CreateTransactionDTO();
    $dto->ledgerId = $data['ledgerId'];
    $dto->type = $data['type'];
    $dto->amount = $data['amount'];
    $dto->currency = $data['currency'];

    $transaction = $this->createTransactionHandler->handle($dto);

    return new JsonResponse([
      'transactionId' => $transaction->getId(),
      'ledgerId' => $transaction->getLedger()->getId(),
      'transactionType' => $transaction->getTransactionType(),
      'amount' => $transaction->getAmount(),
    ]);
  }
}
