<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;

interface AgentListContract extends Arrayable
{
    public function addItem(AgentReadContract $contract): void;

    /**
     * @return list<AgentReadContract>
     */
    public function getItems(): array;

    /**
     * @return list<array{
     *      id: string,
     *      name: string
     *  }>
     */
    public function toArray(): array;
}
