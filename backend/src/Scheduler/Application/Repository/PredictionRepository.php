<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\PredictionListContract;

interface PredictionRepository
{
    public function findAll(): PredictionListContract;
}
