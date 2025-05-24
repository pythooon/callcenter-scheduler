<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;

interface ShiftRepository
{
    public function upsert(ShiftCreateContract $contract): void;

    public function findAll(): ShiftListContract;
}
