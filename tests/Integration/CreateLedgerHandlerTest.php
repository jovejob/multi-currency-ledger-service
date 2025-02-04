<?php

namespace Tests\Integration;

use App\CommandHandler\CreateLedgerHandler;
use App\DTO\CreateLedgerDTO;
use App\Entity\Ledger;
use App\Service\LedgerService;
use PHPUnit\Framework\TestCase;

class CreateLedgerHandlerTest extends TestCase
{
  public function testCreateLedger(): void
  {
    // Arrange
    $mockLedgerService = $this->createMock(LedgerService::class);

    $mockLedger = new Ledger();
    $mockLedger->setName('Test Ledger');
    $mockLedger->setCurrency('USD');
    $mockLedger->setBalance(100.5);

    $mockLedgerService->expects($this->once())
      ->method('createLedger')
      ->with($this->equalTo('Test Ledger'), $this->equalTo('USD'), $this->equalTo(100.5))
      ->willReturn($mockLedger);

    $handler = new CreateLedgerHandler($mockLedgerService);
    $dto = new CreateLedgerDTO('Test Ledger', 'USD', 100.5);

    // Act
    $ledger = $handler->handle($dto);

    // Assert
    $this->assertInstanceOf(Ledger::class, $ledger);
    $this->assertEquals('Test Ledger', $ledger->getName());
    $this->assertEquals('USD', $ledger->getCurrency());
    $this->assertEquals(100.5, $ledger->getBalance());
  }
}
