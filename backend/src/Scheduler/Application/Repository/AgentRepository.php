<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\AgentListContract;

interface AgentRepository
{
    public function findAll(): AgentListContract;
}
