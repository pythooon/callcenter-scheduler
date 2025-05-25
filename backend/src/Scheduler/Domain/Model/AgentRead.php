<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\EfficiencyReadContract;
use App\Scheduler\Application\Contract\QueueListContract;
use Symfony\Component\Uid\Uuid;

final class AgentRead implements AgentReadContract
{
    private EfficiencyListContract $efficiencyListContract;

    public function __construct(
        private readonly Uuid $id,
        private readonly string $name,
        private readonly QueueListContract $queues
    ) {
        $this->efficiencyListContract = new EfficiencyList();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQueues(): QueueListContract
    {
        return $this->queues;
    }

    public function addEfficiency(EfficiencyReadContract $efficiencyReadContract): void
    {
        $this->efficiencyListContract->addItem($efficiencyReadContract);
    }

    public function getEfficiencyListContract(): EfficiencyListContract
    {
        return $this->efficiencyListContract;
    }

    public function getScore(Uuid $queueId): float
    {
        return array_filter(
            $this->efficiencyListContract->getItems(),
            fn(EfficiencyReadContract $efficiencyReadContract) => $efficiencyReadContract->getQueue()->getId(
                ) === $queueId
        )[0]->getScore() ?? 0.0;
    }

    public function calculateEfficiency(CallHistoryListContract $callHistoryListContract): void
    {
        $callsPerQueueAndDay = [];

        foreach ($callHistoryListContract->getItems() as $callHistory) {
            $date = $callHistory->getDate();

            $queueId = (string)$callHistory->getQueue()->getId();
            $dayKey = $date->format('Y-m-d');

            if (!isset($callsPerQueueAndDay[$queueId])) {
                $callsPerQueueAndDay[$queueId] = [];
            }

            $callsPerQueueAndDay[$queueId][$dayKey] = ($callsPerQueueAndDay[$queueId][$dayKey] ?? 0) +
                $callHistory->getCallsCount();
        }

        $this->efficiencyListContract = new EfficiencyList();

        foreach ($this->queues->getItems() as $queue) {
            $queueIdStr = (string)$queue->getId();

            $callsPerDay = $callsPerQueueAndDay[$queueIdStr] ?? [];

            $score = count($callsPerDay) > 0 ? round(array_sum($callsPerDay) / count($callsPerDay), 2) : 0.0;

            $this->efficiencyListContract->addItem(
                new EfficiencyRead(
                    Uuid::v4(),
                    $this,
                    $queue,
                    $score
                )
            );
        }
    }

    /**
     * @return array{
     *     id: string,
     *     name: string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
        ];
    }
}
