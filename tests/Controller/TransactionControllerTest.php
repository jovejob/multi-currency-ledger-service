<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TransactionControllerTest extends WebTestCase
{
  public function testIndex(): void
  {
    $client = static::createClient();
    $client->request('GET', '/transactions');

    self::assertResponseIsSuccessful();
  }

  public function testCreateTransactionValidData(): void
  {
    $client = static::createClient();

    // First, create a ledger to associate with the transaction
    $client->request('POST', '/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
      'name' => 'Test Ledger 1',
      'currency' => 'USD',
      'balance' => 1000.00,
    ]));

    // Assert the ledger creation was successful
    $this->assertResponseIsSuccessful();
    $ledgerResponse = json_decode($client->getResponse()->getContent(), true);
    $ledgerId = $ledgerResponse['id']; // Assuming the response contains the ledger ID


    $client->request('POST', '/transactions', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
      'ledgerId' => $ledgerId,
      'type' => 'credit',
      'amount' => 100.50,
      'currency' => 'USD',
    ]));

    // Assert that the response is successful
    $this->assertResponseIsSuccessful();

    // Assert the response is in JSON format
    $this->assertJson($client->getResponse()->getContent());

    // Check that the response contains the expected transaction details
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $this->assertArrayHasKey('transactionId', $responseData);
    $this->assertArrayHasKey('ledgerId', $responseData);
    $this->assertEquals('credit', $responseData['transactionType']);
    $this->assertEquals(100.50, $responseData['amount']);
  }

  public function testCreateTransactionMissingRequiredField(): void
  {
    $client = static::createClient();
    $client->request('POST', '/transactions', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
      'type' => 'debit',
      'amount' => 50.00,
      'currency' => 'USD',
    ]));

    // Assert that the response returns a 400 status (Bad Request)
    $this->assertResponseStatusCodeSame(400);

    // Assert that the response contains the correct error message for missing fields
    $responseData = json_decode($client->getResponse()->getContent(), true);
    $this->assertArrayHasKey('error', $responseData);
    $this->assertStringContainsString('Missing required fields', $responseData['error']);
  }

  public function testCreateTransactionInvalidCurrency(): void
  {
    $client = static::createClient();

    // First, create a ledger to associate with the transaction
    $client->request('POST', '/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
      'name' => 'Test Ledger 2',
      'currency' => 'USD',
      'balance' => 1000.00,
    ]));

    // Assert the ledger creation was successful
    $this->assertResponseIsSuccessful();
    $ledgerResponse = json_decode($client->getResponse()->getContent(), true);
    $ledgerId = $ledgerResponse['id'];

    // Create a transaction with an invalid currency
    $client->request('POST', '/transactions', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
      'ledgerId' => $ledgerId,
      'type' => 'credit',
      'amount' => 100.00,
      'currency' => 'INVALID', // Invalid currency
    ]));

    // Assert that the response returns a 400 status (Bad Request)
    $this->assertResponseStatusCodeSame(400);

    // Assert that the response contains the correct error message for invalid currency
    // Decode JSON response
    $responseData = json_decode($client->getResponse()->getContent(), true);

    // Assert that the response contains the correct error message for invalid currency
    $this->assertArrayHasKey('error', $responseData);
    $this->assertSame('Currency must be a valid 3-letter ISO code.', $responseData['error']);
  }
}
