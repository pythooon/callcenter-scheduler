<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\ScheduleGenerate;

use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Application\Repository\PredictionRepository;
use App\Scheduler\Application\Repository\ShiftRepository;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\PredictionList;
use App\Scheduler\Domain\Model\PredictionRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\ScheduleCreate;
use App\Scheduler\Domain\UseCase\ScheduleGenerate\ScheduleGenerate;
use App\Scheduler\Domain\Mapper\ShiftMapper;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ScheduleGenerateTest extends TestCase
{
    private EfficiencyRepository $efficiencyRepository;
    private PredictionRepository $predictionRepository;
    private ShiftRepository $shiftRepository;
    private ScheduleGenerate $useCase;

    protected function setUp(): void
    {
        $this->efficiencyRepository = $this->createMock(EfficiencyRepository::class);
        $this->predictionRepository = $this->createMock(PredictionRepository::class);
        $this->shiftRepository = $this->createMock(ShiftRepository::class);

        $this->useCase = new ScheduleGenerate(
            $this->efficiencyRepository,
            $this->predictionRepository,
            $this->shiftRepository,
            new ShiftMapper()
        );
    }

    public function testScheduleGenerationWithLimits(): void
    {
        $queue = new QueueRead(Uuid::v4(), 'Support Queue');
        $queueList = new QueueList();
        $queueList->addItem($queue);

        $agent1 = new AgentRead(Uuid::v4(), 'Alice', $queueList);
        $agent2 = new AgentRead(Uuid::v4(), 'Bob', $queueList);
        $agent3 = new AgentRead(Uuid::v4(), 'Charlie', $queueList);
        $agent4 = new AgentRead(Uuid::v4(), 'Dana', $queueList);

        $efficiencyList = new EfficiencyList();
        foreach ([$agent1, $agent2, $agent3, $agent4] as $i => $agent) {
            $efficiencyList->addItem(new EfficiencyRead(
                Uuid::v4(),
                $agent,
                $queue,
                9.0 - $i,
                new DateTime('2024-01-01'),
                new DateTime('2024-01-07')
            ));
        }

        $this->efficiencyRepository
            ->method('findAll')
            ->willReturn($efficiencyList);

        $predictionList = new PredictionList();
        for ($i = 0; $i < 5; $i++) {
            $predictionList->addItem(new PredictionRead(
                Uuid::v4(),
                $queue,
                new DateTime('2024-01-02'),
                new DateTime("2024-01-02 10:00:00"),
                10
            ));
        }

        $this->predictionRepository
            ->method('findByStartAndEndDate')
            ->willReturn($predictionList);

        $this->shiftRepository
            ->expects($this->exactly(10))
            ->method('upsert');

        $schedule = $this->useCase->run(new ScheduleCreate());

        $this->assertCount(10, $schedule);

        foreach ($schedule as $shift) {
            $this->assertSame('2024-01-02', $shift->getStart()->format('Y-m-d'));
            $this->assertEquals('10:00:00', $shift->getStart()->format('H:i:s'));
            $this->assertSame($queue, $shift->getQueue());

            $this->assertContains(
                $shift->getAgent()->getName(),
                ['Alice', 'Bob', 'Charlie', 'Dana']
            );
        }
    }
}
