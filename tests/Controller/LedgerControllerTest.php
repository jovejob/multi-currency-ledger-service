<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LedgerControllerTest extends WebTestCase
{
  public function testIndex(): void
  {
    $client = static::createClient();
    $client->request('GET', '/ledger');

    self::assertResponseIsSuccessful();
  }

  public function testCreateLedger(): void
  {
    $client = static::createClient();
    $client->request('POST', '/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
      'name' => 'Test Ledger 9',
      'currency' => 'USD',
      'balance' => 100.5,
    ]));

    $this->assertResponseIsSuccessful();
    $this->assertJson($client->getResponse()->getContent());
  }
}
