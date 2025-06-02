<?php

declare(strict_types=1);

namespace App\Scheduler\Application;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Contract\ScheduleCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use DateTimeInterface;

interface SchedulerFacade
{
    public function scheduleGenerate(ScheduleCreateContract $scheduleCreateContract): void;

    public function agents(): AgentListContract;

    public function queues(): QueueListContract;

    public function efficiencies(): EfficiencyListContract;

    public function predictions(): PredictionListContract;

    public function shifts(?DateTimeInterface $startDate = null, ?DateTimeInterface $endDate = null): ShiftListContract;
}
