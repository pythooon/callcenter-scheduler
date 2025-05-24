<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\Shift;

interface ShiftEntityRepository
{
    /**
     * @return list<Shift>
     */
    public function findAll(): array;

    public function upsert(Shift $shift): void;
}
