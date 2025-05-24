<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Contract\CallHistoryReadContract;

class CallHistoryList implements CallHistoryListContract
{
    /**
     * @var list<CallHistoryReadContract>
     */
    private array $items = [];

    public function addItem(CallHistoryReadContract $contract): void
    {
        $this->items[] = $contract;
    }

    /**
     * @return list<CallHistoryReadContract>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return $this->getItems();
    }
}
