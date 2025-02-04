<?php

namespace App\Controller;

use App\CommandHandler\GetLedgerBalanceHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LedgerQueryController extends AbstractController
{

  private GetLedgerBalanceHandler $getLedgerBalanceHandler;

  public function __construct(GetLedgerBalanceHandler $getLedgerBalanceHandler)
  {
    $this->getLedgerBalanceHandler = $getLedgerBalanceHandler;
  }

  #[Route('/ledger/query', name: 'app_ledger_query')]
  public function index(): JsonResponse
  {
    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/LedgerQueryController.php',
    ]);
  }

  #[Route('/balances/{ledgerId}', name: 'get_ledger_balance', methods: ['GET'])]
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
