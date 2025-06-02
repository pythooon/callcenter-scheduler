<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Repository;

use App\Scheduler\Application\Repository\AgentEntityRepository;
use App\Scheduler\Domain\Entity\Agent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Agent>
 */
class AgentEntityRepositoryImpl extends ServiceEntityRepository implements AgentEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agent::class);
    }

    public function findOrFail(Uuid $id): Agent
    {
        $agent = $this->find($id);

        if (!$agent) {
            throw new EntityNotFoundException("Agent with id {$id} not found.");
        }

        return $agent;
    }

    /**
     * @param list<Uuid> $ids
     * @return list<Agent>
     */
    public function findByQueueIds(array $ids = []): array
    {
        $qb = $this->createQueryBuilder('a');

        if (!empty($ids)) {
            $qb->innerJoin('a.queues', 'q')
                ->where('q.id IN (:ids)')
                ->setParameter('ids', array_map(fn(Uuid $id) => $id->toBinary(), $ids));
        }

        /** @var list<Agent> $results */
        $results = $qb->getQuery()->getResult();

        return $results;
    }
}
