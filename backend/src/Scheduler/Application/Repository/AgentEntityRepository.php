<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\Agent;
use Symfony\Component\Uid\Uuid;

interface AgentEntityRepository
{
    /**
     * @return list<Agent>
     */
    public function findAll(): array;

    public function findOrFail(Uuid $id): Agent;

    /**
     * @param list<Uuid> $ids
     * @return list<Agent>
     */
    public function findByQueueIds(array $ids = []): array;
}
