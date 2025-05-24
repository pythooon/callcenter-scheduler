<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;

interface PredictionListContract extends Arrayable
{
    public function addItem(PredictionReadContract $contract): void;

    /**
     * @return list<PredictionReadContract>
     */
    public function getItems(): array;
}
