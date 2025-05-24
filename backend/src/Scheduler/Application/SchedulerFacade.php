<?php

declare(strict_types=1);

namespace App\Scheduler\Application;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use DateTime;

interface SchedulerFacade
{
    public function calculateEfficiency(): EfficiencyListContract;

    public function scheduleGenerate(): void;

    public function agents(): AgentListContract;

    public function queues(): QueueListContract;

    public function efficiencies(): EfficiencyListContract;

    public function predictions(): PredictionListContract;

    public function shifts(?DateTime $start, ?DateTime $end): ShiftListContract;
}
