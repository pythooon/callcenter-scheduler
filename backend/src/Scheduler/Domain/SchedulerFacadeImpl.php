<?php

declare(strict_types=1);

namespace App\Scheduler\Domain;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Contract\ScheduleCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\SchedulerFacade;
use App\Scheduler\Domain\UseCase\Agents\Agents;
use App\Scheduler\Domain\UseCase\CalculateEfficiency\CalculateEfficiency;
use App\Scheduler\Domain\UseCase\Efficiencies\Efficiencies;
use App\Scheduler\Domain\UseCase\Predictions\Predictions;
use App\Scheduler\Domain\UseCase\Queues\Queues;
use App\Scheduler\Domain\UseCase\ScheduleGenerate\ScheduleGenerate;
use App\Scheduler\Domain\UseCase\Shifts\Shifts;
use DateTime;
use DateTimeInterface;
use Throwable;

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

    /**
     * @throws Throwable
     */
    public function scheduleGenerate(ScheduleCreateContract $scheduleCreateContract): void
    {
        $this->calculateEfficiency->run([$scheduleCreateContract->getQueueId()]);
        $this->scheduleGenerate->run($scheduleCreateContract);
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

    public function shifts(?DateTimeInterface $startDate = null, ?DateTimeInterface $endDate = null): ShiftListContract
    {
        return $this->shifts->run($startDate, $endDate);
    }
}
