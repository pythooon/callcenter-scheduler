<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Calculator;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\EfficiencyRead;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final readonly class EfficiencyCalculator
{
    public function calculate(
        AgentReadContract $agent,
        CallHistoryListContract $callHistoryList,
        DateTimeInterface $start,
        DateTimeInterface $end
    ): EfficiencyListContract {
        $callsPerQueueAndHour = [];
        $queuesUsed = [];

        foreach ($callHistoryList->getItems() as $callHistory) {
            $callDate = $callHistory->getDate();

            if ($callDate < $start || $callDate > $end) {
                continue;
            }

            $dateHourKey = $callDate->format('Y-m-d H:00');
            $queue = $callHistory->getQueue();
            $queueId = (string)$queue->getId();
            $queuesUsed[$queueId] = $queue;

            if (!isset($callsPerQueueAndHour[$queueId][$dateHourKey])) {
                $callsPerQueueAndHour[$queueId][$dateHourKey] = 0;
            }

            $callsPerQueueAndHour[$queueId][$dateHourKey] += $callHistory->getCallsCount();
        }

        $efficiencyList = new EfficiencyList();

        foreach ($queuesUsed as $queueId => $queue) {
            $hourlyCalls = $callsPerQueueAndHour[$queueId] ?? [];

            $totalCalls = array_sum($hourlyCalls);
            $hourCount = count($hourlyCalls);

            $score = $hourCount > 0 ? round($totalCalls / $hourCount, 2) : 0.0;

            $efficiencyList->addItem(new EfficiencyRead(
                Uuid::v4(),
                $agent,
                $queue,
                $score,
                $start,
                $end
            ));
        }

        return $efficiencyList;
    }
}
