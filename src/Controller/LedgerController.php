<?php

// src/Controller/LedgerController.php
namespace App\Controller;

use App\DTO\CreateLedgerDTO;
use App\CommandHandler\CreateLedgerHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LedgerController
{
  private CreateLedgerHandler $createLedgerHandler;

  public function __construct(CreateLedgerHandler $createLedgerHandler)
  {
    $this->createLedgerHandler = $createLedgerHandler;
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
}
