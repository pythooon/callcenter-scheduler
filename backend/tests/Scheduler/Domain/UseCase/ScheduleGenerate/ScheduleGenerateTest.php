<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\ScheduleGenerate;

use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Application\Repository\PredictionRepository;
use App\Scheduler\Application\Repository\ShiftRepository;
use App\Scheduler\Domain\Mapper\ShiftMapper;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\PredictionList;
use App\Scheduler\Domain\Model\PredictionRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\UseCase\ScheduleGenerate\ScheduleGenerate;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ScheduleGenerateTest extends TestCase
{
    private MockObject|EfficiencyRepository $efficiencyRepository;
    private MockObject|PredictionRepository $predictionRepository;
    private MockObject|ShiftRepository $shiftRepository;
    private ShiftMapper $shiftMapper;
    private ScheduleGenerate $scheduleGenerate;

    protected function setUp(): void
    {
        $this->efficiencyRepository = $this->createMock(EfficiencyRepository::class);
        $this->predictionRepository = $this->createMock(PredictionRepository::class);
        $this->shiftRepository = $this->createMock(ShiftRepository::class);
        $this->shiftMapper = new ShiftMapper();

        $this->scheduleGenerate = new ScheduleGenerate(
            $this->efficiencyRepository,
            $this->predictionRepository,
            $this->shiftRepository,
            $this->shiftMapper
        );
    }

    public function testCreateWeeklySchedule(): void
    {
        $queueRead = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueList = new QueueList();
        $queueList->addItem($queueRead);
        $agentRead = new AgentRead(Uuid::v4(), 'Agent 1', $queueList);


        $efficiencyRead = new EfficiencyRead(Uuid::v4(), $agentRead, $queueRead, 95.5);

        $efficiencyList = new EfficiencyList();
        $efficiencyList->addItem($efficiencyRead);

        $predictionRead = new PredictionRead(
            Uuid::v4(),
            $queueRead,
            new \DateTime('2025-01-01'),
            new \DateTime('2025-01-01 15:00'),
            40
        );

        $predictionList = new PredictionList();
        $predictionList->addItem($predictionRead);

        $this->efficiencyRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($efficiencyList);

        $this->predictionRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($predictionList);

        $this->shiftRepository
            ->expects($this->exactly(1))
            ->method('upsert')
            ->with($this->isInstanceOf(ShiftCreateContract::class));

        $schedule = $this->scheduleGenerate->createWeeklySchedule();

        $this->assertNotEmpty($schedule);
    }

    public function testGenerateScheduleWithMockedData(): void
    {
        $queueRead = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueList = new QueueList();
        $queueList->addItem($queueRead);
        $agentRead = new AgentRead(Uuid::v4(), 'Agent 1', $queueList);


        $efficiencyRead = new EfficiencyRead(Uuid::v4(), $agentRead, $queueRead, 95.5);

        $efficiencyList = new EfficiencyList();
        $efficiencyList->addItem($efficiencyRead);

        $predictionRead = new PredictionRead(
            Uuid::v4(),
            $queueRead,
            new \DateTime('2025-01-01'),
            new \DateTime('2025-01-01 15:00'),
            40
        );

        $predictionList = new PredictionList();
        $predictionList->addItem($predictionRead);
        $this->efficiencyRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($efficiencyList);

        $this->predictionRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($predictionList);

        $this->shiftRepository
            ->expects($this->exactly(1))
            ->method('upsert')
            ->with($this->isInstanceOf(ShiftCreateContract::class));

        $schedule = $this->scheduleGenerate->createWeeklySchedule();

        $this->assertNotEmpty($schedule);
    }
}
