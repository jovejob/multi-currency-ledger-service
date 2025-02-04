<?php

// src/DTO/CreateTransactionDTO.php
// src/DTO/CreateTransactionDTO.php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTransactionDTO
{
  #[Assert\NotBlank]
  public $ledgerId;

  #[Assert\NotBlank]
  #[Assert\Choice(choices: ['debit', 'credit'])]
  public $type;

  #[Assert\NotBlank]
  #[Assert\Type("numeric")]
  public $amount;

  #[Assert\NotBlank]
  #[Assert\Currency]
  public $currency;
}

