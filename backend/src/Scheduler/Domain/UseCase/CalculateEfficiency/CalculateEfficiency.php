<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\CalculateEfficiency;

use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Repository\AgentRepository;
use App\Scheduler\Application\Repository\CallHistoryRepository;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;

final readonly class CalculateEfficiency
{
    public function __construct(
        private AgentRepository $agentRepository,
        private CallHistoryRepository $callHistoryRepository,
        private EfficiencyRepository $efficiencyRepository,
        private EfficiencyMapper $efficiencyMapper
    ) {
    }

    public function run(): EfficiencyListContract
    {
        $agentListContract = $this->agentRepository->findAll();
        foreach ($agentListContract->getItems() as $agentReadContract) {
            $callHistoryListContract = $this->callHistoryRepository->findByAgentReadContract($agentReadContract);
            $agentReadContract->calculateEfficiency($callHistoryListContract);
            foreach ($agentReadContract->getEfficiencyListContract()->getItems() as $efficiency) {
                $this->efficiencyRepository->upsert(
                    $this->efficiencyMapper->mapReadContractToCreateContract($efficiency)
                );
            }
        }
        return $this->efficiencyRepository->findAll();
    }
}
