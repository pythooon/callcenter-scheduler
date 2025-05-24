<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;

interface QueueListContract extends Arrayable
{
    public function addItem(QueueReadContract $contract): void;

    /**
     * @return list<QueueReadContract>
     */
    public function getItems(): array;
}
