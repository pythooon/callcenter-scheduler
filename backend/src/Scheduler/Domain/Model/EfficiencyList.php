<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\EfficiencyReadContract;

use function array_map;

class EfficiencyList implements EfficiencyListContract
{
    /**
     * @var list<EfficiencyReadContract>
     */
    private array $items = [];

    public function addItem(EfficiencyReadContract $contract): void
    {
        $this->items[] = $contract;
    }

    /**
     * @return list<EfficiencyReadContract>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return array_map(
            fn(EfficiencyReadContract $efficiencyReadContract) => $efficiencyReadContract->toArray(),
            $this->getItems()
        );
    }
}
