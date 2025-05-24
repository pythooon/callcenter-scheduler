<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;

interface ShiftListContract extends Arrayable
{
    public function addItem(ShiftReadContract $contract): void;

    /**
     * @return list<ShiftReadContract>
     */
    public function getItems(): array;
}
