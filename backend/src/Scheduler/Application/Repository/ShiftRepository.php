<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use DateTimeInterface;

interface ShiftRepository
{
    public function upsert(ShiftCreateContract $contract): void;

    public function findShiftsBetweenDates(?DateTimeInterface $start, ?DateTimeInterface $end): ShiftListContract;
}
