<?php

namespace App\Tests\Service;

use App\Service\LedgerService;
use App\Entity\Ledger;
use App\Repository\LedgerRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LedgerServiceTest extends TestCase
{
  private LedgerService $ledgerService;
  private $entityManager;
  private $ledgerRepository;
  private $eventDispatcher;

  protected function setUp(): void
  {
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->ledgerRepository = $this->createMock(LedgerRepository::class);
    $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

    $this->ledgerService = new LedgerService(
      $this->entityManager,
      $this->ledgerRepository,
      $this->eventDispatcher
    );
  }

  public function testCreateLedgerSuccessfully(): void
  {
    $this->ledgerRepository->expects($this->once())
      ->method('findByName')
      ->with('TestLedger')
      ->willReturn(null);

    $this->entityManager->expects($this->once())
      ->method('persist')
      ->with($this->isInstanceOf(Ledger::class));

    $this->entityManager->expects($this->once())
      ->method('flush');

    $ledger = $this->ledgerService->createLedger('TestLedger', 'USD', 100.50);

    $this->assertInstanceOf(Ledger::class, $ledger);
    $this->assertEquals('TestLedger', $ledger->getName());
    $this->assertEquals('USD', $ledger->getCurrency());
    $this->assertEquals(100.50, $ledger->getBalance());
  }

  public function testCreateLedgerThrowsExceptionForDuplicateName(): void
  {
    $existingLedger = new Ledger();
    $existingLedger->setName('TestLedger');

    $this->ledgerRepository->expects($this->once())
      ->method('findByName')
      ->with('TestLedger')
      ->willReturn($existingLedger);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('A ledger with this name already exists.');

    $this->ledgerService->createLedger('TestLedger', 'USD', 100.50);
  }
}
