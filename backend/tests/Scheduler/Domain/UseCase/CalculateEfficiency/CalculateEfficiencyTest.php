<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\CalculateEfficiency;

use App\Scheduler\Application\Repository\AgentRepository;
use App\Scheduler\Application\Repository\CallHistoryRepository;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Domain\Calculator\EfficiencyCalculator;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;
use App\Scheduler\Domain\Model\AgentList;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\CallHistoryList;
use App\Scheduler\Domain\Model\CallHistoryRead;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\UseCase\CalculateEfficiency\CalculateEfficiency;
use DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Uid\Uuid;

class CalculateEfficiencyTest extends TestCase
{
    private AgentRepository $agentRepository;
    private CallHistoryRepository $callHistoryRepository;
    private EfficiencyRepository $efficiencyRepository;
    private CalculateEfficiency $useCase;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->callHistoryRepository = $this->createMock(CallHistoryRepository::class);
        $this->efficiencyRepository = $this->createMock(EfficiencyRepository::class);

        $this->useCase = new CalculateEfficiency(
            $this->agentRepository,
            $this->callHistoryRepository,
            $this->efficiencyRepository,
            new EfficiencyMapper(),
            new EfficiencyCalculator(),
            new NullLogger()
        );
    }

    public function testRunReturnsEfficiencies(): void
    {
        $agentId = Uuid::v4();
        $queue = new QueueRead(Uuid::v4(), 'Queue');
        $queueList = new QueueList();
        $queueList->addItem($queue);

        $agent = new AgentRead($agentId, 'Agent Name', $queueList);
        $agentList = new AgentList();
        $agentList->addItem($agent);

        $this->agentRepository
            ->method('findAll')
            ->willReturn($agentList);

        $callHistory = new CallHistoryRead(
            Uuid::v4(),
            $agent,
            $queue,
            new DateTime('2024-01-01 10:00:00'),
            10
        );
        $callHistoryList = new CallHistoryList();
        $callHistoryList->addItem($callHistory);

        $this->callHistoryRepository
            ->method('findByAgentReadContract')
            ->willReturn($callHistoryList);

        $this->efficiencyRepository
            ->expects($this->once())
            ->method('upsert');
        $this->efficiencyRepository
            ->method('findAll')
            ->willReturnCallback(function () use ($agent, $queue) {
                $list = new EfficiencyList();
                $list->addItem((new EfficiencyCalculator())->calculate(
                    $agent,
                    $this->callHistoryRepository->findByAgentReadContract($agent),
                    new DateTime('2024-01-01 00:00:00'),
                    new DateTime('2024-01-31 23:59:59')
                )->getItems()[0]);
                return $list;
            });

        $result = $this->useCase->run(
            [],
            new DateTime('2024-01-01 00:00:00'),
            new DateTime('2024-01-31 23:59:59')
        );

        $this->assertCount(1, $result->getItems());
        $efficiency = $result->getItems()[0];
        $this->assertSame($agent, $efficiency->getAgent());
        $this->assertSame($queue, $efficiency->getQueue());
        $this->assertSame(10.0, $efficiency->getScore());
    }
}
