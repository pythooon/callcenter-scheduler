<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Repository;

use App\Scheduler\Application\Repository\QueueEntityRepository;
use App\Scheduler\Domain\Entity\Queue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Queue>
 */
class QueueEntityRepositoryImpl extends ServiceEntityRepository implements QueueEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Queue::class);
    }

    public function findOrFail(Uuid $id): Queue
    {
        $queue = $this->find($id);

        if (!$queue) {
            throw new EntityNotFoundException("Agent with id {$id} not found.");
        }

        return $queue;
    }
}
