<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;
use Symfony\Component\Uid\Uuid;

interface CallHistoryRepository
{
    /**
     * @param AgentReadContract $agentReadContract
     * @param list<Uuid> $queueIds
     * @return CallHistoryListContract
     */
    public function findByAgentAndQueues(
        AgentReadContract $agentReadContract,
        array $queueIds
    ): CallHistoryListContract;
}
