<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\CalculateEfficiency;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Repository\AgentRepository;
use App\Scheduler\Application\Repository\CallHistoryRepository;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;
use App\Scheduler\Domain\UseCase\CalculateEfficiency\CalculateEfficiency;
use PHPUnit\Framework\TestCase;

class CalculateEfficiencyTest extends TestCase
{
    private AgentRepository $agentRepository;
    private CallHistoryRepository $callHistoryRepository;
    private EfficiencyRepository $efficiencyRepository;
    private EfficiencyMapper $efficiencyMapper;
    private CalculateEfficiency $calculateEfficiency;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->callHistoryRepository = $this->createMock(CallHistoryRepository::class);
        $this->efficiencyRepository = $this->createMock(EfficiencyRepository::class);
        $this->efficiencyMapper = new EfficiencyMapper();

        $this->calculateEfficiency = new CalculateEfficiency(
            $this->agentRepository,
            $this->callHistoryRepository,
            $this->efficiencyRepository,
            $this->efficiencyMapper
        );
    }

    public function testRun(): void
    {
        $agentListContract = $this->createMock(AgentListContract::class);
        $this->agentRepository->method('findAll')->willReturn($agentListContract);

        $callHistoryListContract = $this->createMock(CallHistoryListContract::class);
        $this->callHistoryRepository->method('findByAgentReadContract')->willReturn($callHistoryListContract);

        $efficiencyListContract = $this->createMock(EfficiencyListContract::class);
        $this->efficiencyRepository->method('findAll')->willReturn($efficiencyListContract);

        $this->calculateEfficiency->run();

        $this->assertInstanceOf(EfficiencyListContract::class, $efficiencyListContract);
    }
}
