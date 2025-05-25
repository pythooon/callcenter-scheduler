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
use App\Scheduler\Domain\Model\EfficiencyList;

final readonly class ScheduleGenerate
{
    private const MAX_WORK_HOURS_PER_DAY = 8;
    private const MAX_AGENTS_PER_PREDICTION = 3;

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
     * @return list<ShiftCreateContract>
     */
    private function createSchedule(
        EfficiencyListContract $efficiencyList,
        PredictionListContract $predictionList
    ): array {
        $schedule = [];
        $agentDailyWorkHours = [];
        $assignedAgents = [];

        foreach ($predictionList->getItems() as $prediction) {
            $agents = $this->findBestAgentsPrediction($efficiencyList, $prediction);
            foreach ($agents as $agent) {
                $agentId = (string)$agent->getId();
                $currentDailyHours = $agentDailyWorkHours[$agentId][$prediction->getDate()->format('Y-m-d')] ?? 0;

                if ($currentDailyHours + 1 <= self::MAX_WORK_HOURS_PER_DAY) {
                    $assignedAgentsForPrediction = $assignedAgents[(string)$prediction->getId()] ?? 0;

                    if ($assignedAgentsForPrediction < self::MAX_AGENTS_PER_PREDICTION) {
                        $shiftCreate = $this->shiftMapper::mapEntityToCreateContract($agent, $prediction);
                        $schedule[] = $shiftCreate;

                        $assignedAgents[(string)$prediction->getId()] = $assignedAgentsForPrediction + 1;
                        $agentDailyWorkHours[$agentId][$prediction->getDate()->format(
                            'Y-m-d'
                        )] = $currentDailyHours + 1;
                        if ($prediction->diffOccupancy($agent->getScore($prediction->getQueue()->getId())) <= 0) {
                            break;
                        }
                    }
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

        foreach ($efficiencyList->getItems() as $efficiency) {
            if ($efficiency->getQueue()->getId() === $prediction->getQueue()->getId()) {
                $agent = $efficiency->getAgent();
                $agent->addEfficiency($efficiency);
                $agents[] = $agent;
            }
        }

        return $agents;
    }

    /**
     * @param list<ShiftCreateContract> $schedule
     */
    private function saveSchedule(array $schedule): void
    {
        foreach ($schedule as $shiftCreate) {
            $this->shiftRepository->upsert($shiftCreate);
        }
    }
}
