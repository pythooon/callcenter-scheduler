<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Contract\QueueReadContract;

final class QueueList implements QueueListContract
{
    /**
     * @var list<QueueReadContract>
     */
    private array $items = [];

    public function addItem(QueueReadContract $contract): void
    {
        $this->items[] = $contract;
    }

    /**
     * @return list<QueueReadContract>
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
