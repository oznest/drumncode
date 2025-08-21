<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Domain\EventReleaseInterface;

class DoctrineFlushListener
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->em->getUnitOfWork()->getIdentityMap() as $className => $entities) {
            foreach ($entities as $entity) {
                if (!($entity instanceof EventReleaseInterface)) {
                    continue;
                }

                foreach ($entity->releaseEvents() as $event) {
                    $this->eventDispatcher->dispatch($event);
                }
            }
        }
    }
}
