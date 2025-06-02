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
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\UseCase\CalculateEfficiency\CalculateEfficiency;
use DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class CalculateEfficiencyTest extends TestCase
{
    private AgentRepository $agentRepository;
    private CallHistoryRepository $callHistoryRepository;
    private EfficiencyRepository $efficiencyRepository;
    private EfficiencyMapper $efficiencyMapper;
    private EfficiencyCalculator $efficiencyCalculator;
    private LoggerInterface $logger;
    private CalculateEfficiency $useCase;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->callHistoryRepository = $this->createMock(CallHistoryRepository::class);
        $this->efficiencyRepository = $this->createMock(EfficiencyRepository::class);
        $this->efficiencyMapper = new EfficiencyMapper();
        $this->efficiencyCalculator = new EfficiencyCalculator();
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new CalculateEfficiency(
            $this->agentRepository,
            $this->callHistoryRepository,
            $this->efficiencyRepository,
            $this->efficiencyMapper,
            $this->efficiencyCalculator,
            $this->logger
        );
    }

    public function testEfficiencyCalculationForAgents(): void
    {
        $queue = new QueueRead(Uuid::v4(), 'Support');
        $queueList = new QueueList();
        $queueList->addItem($queue);

        $agent = new AgentRead(Uuid::v4(), 'Test Agent', $queueList);
        $agentList = new AgentList();
        $agentList->addItem($agent);

        $this->agentRepository
            ->method('findAll')
            ->willReturn($agentList);

        $callHistoryList = new CallHistoryList();
        $callHistoryList->addItem(new CallHistoryRead(
            Uuid::v4(),
            $agent,
            $queue,
            new \DateTimeImmutable('2025-05-30 14:00:00'),
            15
        ));

        $this->callHistoryRepository
            ->method('findByAgentAndQueues')
            ->willReturn($callHistoryList);

        $this->efficiencyRepository
            ->expects($this->once())
            ->method('upsert');

        $this->efficiencyRepository
            ->method('findAll')
            ->willReturnCallback(function () use ($agent, $queue) {
                $eff = new EfficiencyRead(
                    Uuid::v4(),
                    $agent,
                    $queue,
                    7.8,
                    new \DateTime('2024-01-01'),
                    new \DateTime('2024-01-31')
                );
                $list = new EfficiencyList();
                $list->addItem($eff);
                return $list;
            });

        $result = $this->useCase->run();

        $this->assertCount(1, $result->getItems());
        $this->assertSame(7.8, $result->getItems()[0]->getScore());
        $this->assertSame($agent, $result->getItems()[0]->getAgent());
    }

    public function testHandlesEfficiencyCalculationExceptionForOneAgent(): void
    {
        $queue = new QueueRead(Uuid::v4(), 'Queue');
        $queueList = new QueueList();
        $queueList->addItem($queue);
        $agent = new AgentRead(Uuid::v4(), 'Problem Agent', $queueList);

        $agentList = new AgentList();
        $agentList->addItem($agent);

        $this->agentRepository
            ->method('findAll')
            ->willReturn($agentList);

        // Symulacja wyjątku przy pobieraniu historii – to wejdzie w catch w useCase
        $this->callHistoryRepository
            ->method('findByAgentAndQueues')
            ->willThrowException(new \RuntimeException('Failed'));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('Failed to calculate efficiency for agent'),
                $this->arrayHasKey('agentId')
            );

        $this->efficiencyRepository
            ->method('findAll')
            ->willReturn(new EfficiencyList());

        $result = $this->useCase->run([]);

        $this->assertCount(0, $result->getItems());
    }
}
