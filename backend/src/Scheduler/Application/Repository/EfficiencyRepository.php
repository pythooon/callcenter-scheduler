<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\EfficiencyCreateContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;

interface EfficiencyRepository
{
    public function upsert(EfficiencyCreateContract $contract): void;

    public function findAll(): EfficiencyListContract;
}
