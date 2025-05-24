<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;

interface CallHistoryListContract extends Arrayable
{
    public function addItem(CallHistoryReadContract $contract): void;

    /**
     * @return list<CallHistoryReadContract>
     */
    public function getItems(): array;
}
