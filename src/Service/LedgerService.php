<?php


namespace App\Service;

use App\Entity\Ledger;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LedgerRepository;
use App\Event\LedgerCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LedgerService
{
  private EntityManagerInterface $em;
  private LedgerRepository $ledgerRepository;
  private EventDispatcherInterface $eventDispatcher;

  public function __construct(
    EntityManagerInterface $em,
    LedgerRepository $ledgerRepository,
    EventDispatcherInterface $eventDispatcher
  ) {
    $this->em = $em;
    $this->ledgerRepository = $ledgerRepository;
    $this->eventDispatcher = $eventDispatcher;
  }

  public function createLedger(string $name, string $currency): Ledger
  {
    // todo extract a validation class maybe to refactor this further
    if ($this->ledgerRepository->findByName($name)) {
      throw new \Exception("A ledger with this name already exists.");
    }

    $ledger = new Ledger();
    $ledger->setName($name);
    $ledger->setCurrency($currency);

    // Persist in the database
    $this->em->persist($ledger);
    $this->em->flush();

    // ✅ Additional business logic like logging,
    // queueing, external API calls, notifications,
    // or other business logic
    //
    // Example: Dispatch an event after creating a ledger
    // $event = new LedgerCreatedEvent($ledger);
    // $this->eventDispatcher->dispatch($event);

    return $ledger;
  }
}
