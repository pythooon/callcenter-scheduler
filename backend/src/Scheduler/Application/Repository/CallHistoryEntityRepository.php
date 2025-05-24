<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\CallHistory;
use Symfony\Component\Uid\Uuid;

interface CallHistoryEntityRepository
{
    /**
     * @return list<CallHistory>
     */
    public function findByAgentId(Uuid $agentId): array;
}
