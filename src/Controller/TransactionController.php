<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\CreateTransactionDTO;
use App\CommandHandler\CreateTransactionHandler;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class TransactionController extends AbstractController
{

  private CreateTransactionHandler $createTransactionHandler;
  private LoggerInterface $logger;

  public function __construct(CreateTransactionHandler $createTransactionHandler, LoggerInterface $logger)
  {
    $this->createTransactionHandler = $createTransactionHandler;
    $this->logger = $logger;
  }

  #[Route('/transactions', methods: ['POST'], name: 'create_transaction')]
  public function createTransaction(Request $request, RateLimiterFactory $anonymousApiLimiter): JsonResponse
  {
    $clientIp = $request->getClientIp();

    // Create a limiter based on a unique identifier (client's IP)
    $limiter = $anonymousApiLimiter->create($clientIp);

    // Check if the rate limit is exceeded
    $limitInfo = $limiter->consume(1);
    if (false === $limitInfo->isAccepted()) {
      $this->logger->warning("Rate limit exceeded for IP: $clientIp", [
        'remaining_tokens' => $limitInfo->getRemainingTokens(),
        'retry_after' => $limitInfo->getRetryAfter()?->getTimestamp(),
      ]);

      throw new TooManyRequestsHttpException(null, "Rate limit exceeded. Try again later.");
    }

    // Log the request
    $this->logger->info("Processing transaction request", [
      'ip' => $clientIp,
      'request_body' => $request->getContent(),
    ]);

    // Decode the request content to create the DTO
    $data = json_decode($request->getContent(), true);

    $dto = new CreateTransactionDTO();
    $dto->ledgerId = $data['ledgerId'] ?? null;
    $dto->type = $data['type'] ?? null;
    $dto->amount = $data['amount'] ?? null;
    $dto->currency = $data['currency'] ?? null;

    // Pass the DTO to the handler and return the response
    $response = $this->createTransactionHandler->handle($dto);

    // Log successful transaction
    $this->logger->info("Transaction successfully processed", [
      'ledgerId' => $dto->ledgerId,
      'type' => $dto->type,
      'amount' => $dto->amount,
      'currency' => $dto->currency,
    ]);

    return $response;
  }

  // #[Route('/transactions', methods: ['POST'], name: 'create_transaction')]
  // public function createTransaction(Request $request, RateLimiterFactory $anonymousApiLimiter): JsonResponse
  // {
  //   // Create a limiter based on a unique identifier (client's IP or another unique identifier)
  //   $limiter = $anonymousApiLimiter->create($request->getClientIp());

  //   // Check if the rate limit is exceeded (consume 1 token)
  //   if (false === $limiter->consume(1)->isAccepted()) {
  //     throw new TooManyRequestsHttpException();
  //   }

  //   // Decode the request content to create the DTO
  //   $data = json_decode($request->getContent(), true);

  //   $dto = new CreateTransactionDTO();
  //   $dto->ledgerId = $data['ledgerId'] ?? null;
  //   $dto->type = $data['type'] ?? null;
  //   $dto->amount = $data['amount'] ?? null;
  //   $dto->currency = $data['currency'] ?? null;

  //   // Pass the DTO to the handler and return the response
  //   return $this->createTransactionHandler->handle($dto);
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
