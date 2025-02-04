<?php

// src/DTO/CreateLedgerDTO.php
namespace App\DTO;

class CreateLedgerDTO
{
  public string $name;
  public string $currency;
  public ?float $balance; // Optional balance field

  public function __construct(string $name, string $currency, ?float $balance = null)
  {
    $this->name = $name;
    $this->currency = $currency;
    $this->balance = $balance;
  }
}
