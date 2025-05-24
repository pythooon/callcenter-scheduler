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
}
