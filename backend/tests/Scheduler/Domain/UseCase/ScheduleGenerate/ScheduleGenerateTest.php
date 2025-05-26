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

    public function testCreateWeeklyScheduleGeneratesValidShifts(): void
    {
        $queue = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueList = new QueueList();
        $queueList->addItem($queue);

        $agent1 = new AgentRead(Uuid::v4(), 'Agent One', $queueList);
        $agent2 = new AgentRead(Uuid::v4(), 'Agent Two', $queueList);

        $efficiencyList = new EfficiencyList();
        $efficiencyList->addItem(new EfficiencyRead(
            Uuid::v4(),
            $agent1,
            $queue,
            8.5,
            new DateTime('2024-01-01'),
            new DateTime('2024-01-07')
        ));
        $efficiencyList->addItem(new EfficiencyRead(
            Uuid::v4(),
            $agent2,
            $queue,
            7.0,
            new DateTime('2024-01-01'),
            new DateTime('2024-01-07')
        ));

        $this->efficiencyRepository
            ->method('findAll')
            ->willReturn($efficiencyList);

        $predictionList = new PredictionList();
        $predictionDate = new DateTime('2024-01-02');
        $predictionTime = new DateTime('2024-01-02 10:00:00');

        $prediction = new PredictionRead(
            Uuid::v4(),
            $queue,
            $predictionDate,
            $predictionTime,
            10
        );

        $predictionList->addItem($prediction);

        $this->predictionRepository
            ->method('findAll')
            ->willReturn($predictionList);

        $this->shiftRepository
            ->expects($this->atLeastOnce())
            ->method('upsert');

        $shifts = $this->useCase->createWeeklySchedule();

        $this->assertNotEmpty($shifts);
        $this->assertCount(2, $shifts);

        foreach ($shifts as $shift) {
            $this->assertSame($queue, $shift->getQueue());
            $this->assertEquals(new DateTime($predictionDate->format('Y-m-d') . ' '. $predictionTime->format('H:i')), $shift->getStart());
            $this->assertContains($shift->getAgent()->getName(), ['Agent One', 'Agent Two']);
        }
    }
}
