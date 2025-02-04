<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\CreateLedgerDTO;
use App\CommandHandler\CreateLedgerHandler;

final class LedgerController extends AbstractController
{

  private CreateLedgerHandler $createLedgerHandler;

  public function __construct(CreateLedgerHandler $createLedgerHandler)
  {
    $this->createLedgerHandler = $createLedgerHandler;
  }

  #[Route('/ledger', name: 'app_ledger')]
  public function index(): JsonResponse
  {
    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/LedgerController.php',
    ]);
  }

  #[Route('/ledgers', name: 'create_ledger', methods: ['POST'], format: 'json')]
  public function createLedger(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);
    $dto = new CreateLedgerDTO($data['name'], $data['currency']);
    $dto->name = $data['name'];
    $dto->currency = $data['currency'];

    $ledger = $this->createLedgerHandler->handle($dto);

    return new JsonResponse(['id' => $ledger->getId(), 'name' => $ledger->getName(), 'currency' => $ledger->getCurrency()]);
  }
}
