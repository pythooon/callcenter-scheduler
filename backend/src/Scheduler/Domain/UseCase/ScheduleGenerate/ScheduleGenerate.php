<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\ScheduleGenerate;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\EfficiencyReadContract;
use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Application\Repository\PredictionRepository;
use App\Scheduler\Application\Repository\ShiftRepository;
use App\Scheduler\Domain\Mapper\ShiftMapper;

final readonly class ScheduleGenerate
{
    private const MAX_WORK_HOURS_PER_DAY = 8;

    public function __construct(
        private EfficiencyRepository $efficiencyRepository,
        private PredictionRepository $predictionRepository,
        private ShiftRepository $shiftRepository,
        private ShiftMapper $shiftMapper
    ) {
    }

    public function createWeeklySchedule(): void
    {
        $efficiencyListContract = $this->efficiencyRepository->findAll();
        $predictionListContract = $this->predictionRepository->findAll();

        $schedule = $this->createSchedule($efficiencyListContract, $predictionListContract);

        $this->saveSchedule($schedule);
    }

    /**
     * @param EfficiencyListContract $efficiencyList
     * @param PredictionListContract $predictionList
     * @return list<ShiftCreateContract>
     */
    private function createSchedule(
        EfficiencyListContract $efficiencyList,
        PredictionListContract $predictionList
    ): array {
        $schedule = [];
        $agentDailyWorkHours = [];

        foreach ($predictionList->getItems() as $prediction) {
            $agents = $this->findBestAgentsPrediction($efficiencyList, $prediction);
            foreach ($agents as $agent) {
                $agentId = (string) $agent->getId();
                $currentDailyHours = $agentDailyWorkHours[$agentId] ?? 0;

                if ($currentDailyHours + 1 <= self::MAX_WORK_HOURS_PER_DAY) {
                    $shiftCreate = $this->shiftMapper::mapEntityToCreateContract($agent, $prediction);
                    $schedule[] = $shiftCreate;
                    $agentDailyWorkHours[$agentId] = $currentDailyHours + 1;
                }
            }
        }

        return $schedule;
    }

    /**
     * @return list<AgentReadContract>
     */
    private function findBestAgentsPrediction(
        EfficiencyListContract $efficiencyList,
        PredictionReadContract $prediction
    ): array {
        $agents = [];
        $efficiencies = [];

        foreach ($efficiencyList->getItems() as $efficiency) {
            if ($efficiency->getQueue()->getId() === $prediction->getQueue()->getId()) {
                $efficiencies[] = $efficiency;
            }
        }

        usort($efficiencies, function (EfficiencyReadContract $a, EfficiencyReadContract $b) {
            return $b->getScore() <=> $a->getScore();
        });

        foreach ($efficiencies as $efficiency) {
            $agents[] = $efficiency->getAgent();
        }

        return $agents;
    }

    /**
     * @param list<ShiftCreateContract> $schedule
     * @return void
     */
    private function saveSchedule(array $schedule): void
    {
        foreach ($schedule as $shiftCreate) {
            $this->shiftRepository->upsert($shiftCreate);
        }
    }
}
