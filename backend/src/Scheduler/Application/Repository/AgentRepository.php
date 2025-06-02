<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\AgentListContract;
use Symfony\Component\Uid\Uuid;

interface AgentRepository
{
    public function findAll(): AgentListContract;

    /**
     * @param list<Uuid> $ids
     * @return AgentListContract
     */
    public function findByQueueIds(array $ids = []): AgentListContract;
}
