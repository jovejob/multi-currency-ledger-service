<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\CreateTransactionDTO;
use App\CommandHandler\CreateTransactionHandler;

final class TransactionController extends AbstractController
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
    $dto->ledgerId = $data['ledgerId'] ?? null;
    $dto->type = $data['type'] ?? null;
    $dto->amount = $data['amount'] ?? null;
    $dto->currency = $data['currency'] ?? null;

    // Pass the DTO to the handler
    return $this->createTransactionHandler->handle($dto);
  }

  // #[Route('/transactions', methods: ['POST'], name: 'create_transaction')]
  // public function createTransaction(Request $request): JsonResponse
  // {
  //   $data = json_decode($request->getContent(), true);

  //   $dto = new CreateTransactionDTO();
  //   $dto->ledgerId = $data['ledgerId'] ?? null;
  //   $dto->type = $data['type'] ?? null;
  //   $dto->amount = $data['amount'] ?? null;
  //   $dto->currency = $data['currency'] ?? null;

  //   // Pass the DTO to the handler
  //   return $this->createTransactionHandler->handle($dto);
  // }

  // #[Route('/transactions', methods: ['POST'], name: 'create_transaction')]
  // public function createTransaction(Request $request): JsonResponse
  // {
  //   $data = json_decode($request->getContent(), true);
  //   $dto = new CreateTransactionDTO();
  //   $dto->ledgerId = $data['ledgerId'];
  //   $dto->type = $data['type'];
  //   $dto->amount = $data['amount'];
  //   $dto->currency = $data['currency'];

  //   $transaction = $this->createTransactionHandler->handle($dto);

  //   return new JsonResponse([
  //     'transactionId' => $transaction->getId(),
  //     'ledgerId' => $transaction->getLedger()->getId(),
  //     'transactionType' => $transaction->getTransactionType(),
  //     'amount' => $transaction->getAmount(),
  //   ]);
  // }

  // todo list transactions
  #[Route('/transactions', name: 'app_transaction')]


  public function index(): JsonResponse
  {
    return $this->json([
      'message' => 'List of transactions (in progress..)',
      'path' => 'src/Controller/TransactionController.php',
    ]);
  }
}
