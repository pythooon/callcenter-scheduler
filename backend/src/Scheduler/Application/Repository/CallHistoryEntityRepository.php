<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\CallHistory;
use Symfony\Component\Uid\Uuid;

interface CallHistoryEntityRepository
{
    /**
     * @param Uuid $agentId
     * @param list<Uuid> $queueIds
     * @return list<CallHistory>
     */
    public function findByAgentIdAndQueueIds(Uuid $agentId, array $queueIds): array;
}
