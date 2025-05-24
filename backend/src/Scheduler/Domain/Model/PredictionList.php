<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\PredictionReadContract;

class PredictionList implements PredictionListContract
{
    /**
     * @var list<PredictionReadContract>
     */
    private array $items = [];

    public function addItem(PredictionReadContract $contract): void
    {
        $this->items[] = $contract;
    }

    /**
     * @return list<PredictionReadContract>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return array_map(
            fn(PredictionReadContract $efficiencyReadContract) => $efficiencyReadContract->toArray(),
            $this->getItems()
        );
    }
}
