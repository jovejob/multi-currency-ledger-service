<?php

// src/DTO/CreateTransactionDTO.php
namespace App\DTO;

class CreateTransactionDTO
{
  public string $ledgerId;
  public string $type; // debit/credit
  public float $amount;
  public string $currency;
}
