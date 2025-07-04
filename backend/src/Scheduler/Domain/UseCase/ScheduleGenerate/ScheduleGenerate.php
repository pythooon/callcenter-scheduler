<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\ScheduleGenerate;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Application\Contract\ScheduleCreateContract;
use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Application\Repository\PredictionRepository;
use App\Scheduler\Application\Repository\ShiftRepository;
use App\Scheduler\Domain\Mapper\ShiftMapper;

final readonly class ScheduleGenerate
{
    private const int MAX_WORK_HOURS_PER_DAY = 8;
    private const int MAX_AGENTS_PER_PREDICTION = 3;

    public function __construct(
        private EfficiencyRepository $efficiencyRepository,
        private PredictionRepository $predictionRepository,
        private ShiftRepository $shiftRepository,
        private ShiftMapper $shiftMapper
    ) {
    }

    /**
     * @return list<ShiftCreateContract>
     */
    public function run(ScheduleCreateContract $scheduleCreateContract): array
    {
        $efficiencyList = $this->prepareEfficiencyList($scheduleCreateContract);
        $predictionList = $this->predictionRepository->findByStartAndEndDate(
            $scheduleCreateContract->getStartDate(),
            $scheduleCreateContract->getEndDate(),
            $scheduleCreateContract->getQueueId()
        );

        $schedule = $this->generateSchedule($efficiencyList, $predictionList);

        foreach ($schedule as $shiftCreate) {
            $this->shiftRepository->upsert($shiftCreate);
        }

        return $schedule;
    }

    /**
     * @return list<ShiftCreateContract>
     */
    private function generateSchedule(
        EfficiencyListContract $efficiencyList,
        PredictionListContract $predictionList
    ): array {
        $schedule = [];
        $agentDailyWorkHours = [];
        $assignedAgents = [];

        foreach ($predictionList->getItems() as $prediction) {
            foreach ($this->findBestAgentsPrediction($efficiencyList, $prediction) as $agent) {
                $agentId = (string)$agent->getId();
                $currentDailyHours = $agentDailyWorkHours[$agentId][$prediction->getDate()->format('Y-m-d H:00')] ?? 0;

                if ($currentDailyHours >= self::MAX_WORK_HOURS_PER_DAY) {
                    continue;
                }

                $assignedAgentsForPrediction = $assignedAgents[(string)$prediction->getId()] ?? 0;

                if ($assignedAgentsForPrediction >= self::MAX_AGENTS_PER_PREDICTION) {
                    continue;
                }

                $schedule[] = $this->shiftMapper::mapEntityToCreateContract($agent, $prediction);

                $assignedAgents[(string)$prediction->getId()] = $assignedAgentsForPrediction + 1;
                $agentDailyWorkHours[$agentId][$prediction->getDate()->format('Y-m-d')] = $currentDailyHours + 1;

                if ($prediction->diffOccupancy($agent->getScore($prediction->getQueue()->getId())) <= 0) {
                    break;
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
            if ($efficiency->getQueue()->getId() !== $prediction->getQueue()->getId()) {
                continue;
            }

            $agent = $efficiency->getAgent();
            $agent->addEfficiency($efficiency);
            $agents[] = $agent;
        }

        return $agents;
    }

    private function prepareEfficiencyList(ScheduleCreateContract $scheduleCreateContract): EfficiencyListContract
    {
        return $scheduleCreateContract->getQueueId() === null ?
            $this->efficiencyRepository->findAll() :
            $this->efficiencyRepository->findByQueueId($scheduleCreateContract->getQueueId());
    }
}
