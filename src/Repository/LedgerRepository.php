<?php

namespace App\Repository;

use App\Entity\Ledger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ledger>
 */
class LedgerRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Ledger::class);
  }

  public function findByName(string $name): ?Ledger
  {
    return $this->findOneBy(['name' => $name]);
  }

  public function getBalance(int $ledgerId): ?float
  {
    $ledger = $this->find($ledgerId);
    return $ledger ? $ledger->getBalance() : null;
  }
}
