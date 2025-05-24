<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;

interface CallHistoryRepository
{
    public function findByAgentReadContract(AgentReadContract $agentReadContract): CallHistoryListContract;
}
