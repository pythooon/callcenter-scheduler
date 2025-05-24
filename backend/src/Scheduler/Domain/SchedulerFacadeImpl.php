<?php

declare(strict_types=1);

namespace App\Scheduler\Domain;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\SchedulerFacade;
use App\Scheduler\Domain\UseCase\Agents\Agents;
use App\Scheduler\Domain\UseCase\CalculateEfficiency\CalculateEfficiency;
use App\Scheduler\Domain\UseCase\Efficiencies\Efficiencies;
use App\Scheduler\Domain\UseCase\Predictions\Predictions;
use App\Scheduler\Domain\UseCase\Queues\Queues;
use App\Scheduler\Domain\UseCase\ScheduleGenerate\ScheduleGenerate;
use App\Scheduler\Domain\UseCase\Shifts\Shifts;
use DateTimeInterface;

final readonly class SchedulerFacadeImpl implements SchedulerFacade
{
    public function __construct(
        private CalculateEfficiency $calculateEfficiency,
        private ScheduleGenerate $scheduleGenerate,
        private Agents $agents,
        private Queues $queues,
        private Efficiencies $efficiencies,
        private Predictions $predictions,
        private Shifts $shifts,
    ) {
    }

    public function calculateEfficiency(): EfficiencyListContract
    {
        return $this->calculateEfficiency->run();
    }

    public function scheduleGenerate(): void
    {
        $this->scheduleGenerate->createWeeklySchedule();
    }

    public function agents(): AgentListContract
    {
        return $this->agents->run();
    }

    public function queues(): QueueListContract
    {
        return $this->queues->run();
    }

    public function efficiencies(): EfficiencyListContract
    {
        return $this->efficiencies->run();
    }

    public function predictions(): PredictionListContract
    {
        return $this->predictions->run();
    }

    public function shifts(?DateTimeInterface $start, ?DateTimeInterface $end): ShiftListContract
    {
        return $this->shifts->run($start, $end);
    }
}
