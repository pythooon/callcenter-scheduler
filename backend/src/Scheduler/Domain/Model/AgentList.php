<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Contract\AgentReadContract;

use function array_map;

final class AgentList implements AgentListContract
{
    /**
     * @var list<AgentReadContract>
     */
    private array $items = [];

    public function addItem(AgentReadContract $contract): void
    {
        $this->items[] = $contract;
    }

    /**
     * @return list<AgentReadContract>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return list<array{
     *      id: string,
     *      name: string
     *  }>
     */
    public function toArray(): array
    {
        return array_map(fn(AgentReadContract $agentReadContract) => $agentReadContract->toArray(), $this->getItems());
    }
}
