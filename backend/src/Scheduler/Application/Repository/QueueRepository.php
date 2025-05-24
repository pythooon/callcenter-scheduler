<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\QueueListContract;

interface QueueRepository
{
    public function findAll(): QueueListContract;
}
