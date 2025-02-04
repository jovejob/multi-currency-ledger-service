<?php

// src/DTO/CreateLedgerDTO.php
namespace App\DTO;

class CreateLedgerDTO
{
  public string $name;
  public string $currency;

  public function __construct(string $name, string $currency)
  {
    $this->name = $name;
    $this->currency = $currency;
  }
}
