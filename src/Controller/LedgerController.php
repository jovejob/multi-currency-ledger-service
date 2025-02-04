<?php

// src/Controller/LedgerController.php
namespace App\Controller;

use App\CommandHandler\GetLedgerBalanceHandler;
use App\DTO\CreateLedgerDTO;
use App\CommandHandler\CreateLedgerHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LedgerController
{
  private CreateLedgerHandler $createLedgerHandler;
  private GetLedgerBalanceHandler $getLedgerBalanceHandler;

  public function __construct(CreateLedgerHandler $createLedgerHandler, GetLedgerBalanceHandler $getLedgerBalanceHandler)
  {
    $this->createLedgerHandler = $createLedgerHandler;
    $this->getLedgerBalanceHandler = $getLedgerBalanceHandler;
  }

  /**
   * @Route("/ledgers", methods={"POST"})
   */
  public function createLedger(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $dto = new CreateLedgerDTO();
    $dto->name = $data['name'];
    $dto->currency = $data['currency'];

    $ledger = $this->createLedgerHandler->handle($dto);

    return new JsonResponse(['id' => $ledger->getId(), 'name' => $ledger->getName(), 'currency' => $ledger->getCurrency()]);
  }


  // todo temp (move to its own service/controller to not break CQRS pattern)
  /**
   * @Route("/balances/{ledgerId}", methods={"GET"})
   */
  public function getBalance(int $ledgerId): JsonResponse
  {
    $ledger = $this->getLedgerBalanceHandler->handle($ledgerId);

    if (!$ledger) {
      return new JsonResponse(['error' => 'Ledger not found'], 404);
    }

    return new JsonResponse([
      'ledgerId' => $ledger->getId(),
      'balance' => $ledger->getBalance(),
      'currency' => $ledger->getCurrency(),
    ]);
  }

}
