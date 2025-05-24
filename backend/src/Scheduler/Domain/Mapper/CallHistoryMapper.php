<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Mapper;

use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Contract\CallHistoryReadContract;
use App\Scheduler\Domain\Entity\CallHistory;
use App\Scheduler\Domain\Model\CallHistoryList;
use App\Scheduler\Domain\Model\CallHistoryRead;

class CallHistoryMapper
{
    /**
     * @param list<CallHistory> $items
     */
    public static function mapArrayToListContract(array $items): CallHistoryListContract
    {
        $queueList = new CallHistoryList();
        foreach ($items as $item) {
            $queueList->addItem(self::mapEntityToReadContract($item));
        }

        return $queueList;
    }

    public static function mapEntityToReadContract(CallHistory $queueEntity): CallHistoryReadContract
    {
        $agentReadContract = AgentMapper::mapEntityToReadContract($queueEntity->getAgent());
        $queueReadContract = QueueMapper::mapEntityToReadContract($queueEntity->getQueue());
        return new CallHistoryRead(
            id: $queueEntity->getId(),
            agent: $agentReadContract,
            queue: $queueReadContract,
            date: $queueEntity->getDate(),
            callsCount: $queueEntity->getCallsCount()
        );
    }
}
