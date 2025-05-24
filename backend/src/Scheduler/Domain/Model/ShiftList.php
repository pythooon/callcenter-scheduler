<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Contract\ShiftReadContract;
use App\Scheduler\Domain\Entity\Shift;

use function array_map;

final class ShiftList implements ShiftListContract
{
    /**
     * @var list<ShiftReadContract>
     */
    private array $items;

    public function addItem(ShiftReadContract $contract): void
    {
        $this->items[] = $contract;
    }

    /**
     * @return list<ShiftReadContract>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function toArray(): array
    {
        return array_map(fn(ShiftReadContract $shiftReadContract) => $shiftReadContract->toArray(), $this->getItems());
    }
}
