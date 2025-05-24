<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;

interface EfficiencyListContract extends Arrayable
{
    public function addItem(EfficiencyReadContract $contract): void;

    /**
     * @return list<EfficiencyReadContract>
     */
    public function getItems(): array;
}
