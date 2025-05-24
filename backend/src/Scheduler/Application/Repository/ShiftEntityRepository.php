<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\Shift;
use DateTimeInterface;

interface ShiftEntityRepository
{
    /**
     * @return list<Shift>
     */
    public function findShiftsBetweenDates(?DateTimeInterface $start, ?DateTimeInterface $end): array;

    public function upsert(Shift $shift): void;
}
