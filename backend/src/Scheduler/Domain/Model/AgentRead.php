<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\EfficiencyReadContract;
use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Contract\QueueReadContract;
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
        )[0]->getScore();
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     queues: string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->id,
            'name' => $this->name,
            'queues' => implode(
                ',',
                array_map(
                    fn(QueueReadContract $queueReadContract) => $queueReadContract->getName(),
                    $this->queues->getItems()
                )
            ),
        ];
    }
}
