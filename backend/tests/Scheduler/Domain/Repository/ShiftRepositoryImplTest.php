<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Repository\ShiftEntityRepository;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Shift;
use App\Scheduler\Domain\Mapper\ShiftMapper;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\ShiftCreate;
use App\Scheduler\Domain\Repository\ShiftRepositoryImpl;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ShiftRepositoryImplTest extends TestCase
{
    private ShiftEntityRepository $entityRepository;
    private ShiftMapper $mapper;
    private ShiftRepositoryImpl $repository;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(ShiftEntityRepository::class);
        $this->mapper = new ShiftMapper();
        $this->repository = new ShiftRepositoryImpl($this->entityRepository, $this->mapper);
    }

    public function testFindShiftsBetweenDates(): void
    {
        $startDate = new DateTime('2025-05-01 00:00:00');
        $endDate = new DateTime('2025-05-07 23:59:59');

        $queue1 = new Queue();
        $queue1->setId(Uuid::v4());
        $queue1->setName('Queue 1');

        $queue2 = new Queue();
        $queue2->setId(Uuid::v4());
        $queue2->setName('Queue 2');

        $agent1 = new Agent();
        $agent1->setId(Uuid::v4());
        $agent1->setName('Agent 1');

        $agent2 = new Agent();
        $agent2->setId(Uuid::v4());
        $agent2->setName('Agent 2');

        $shift1 = new Shift();
        $shift1->setId(Uuid::v4());
        $shift1->setAgent($agent1);
        $shift1->setStart(new DateTime('2025-05-01 08:00:00'));
        $shift1->setEnd(new DateTime('2025-05-01 16:00:00'));
        $shift1->setQueue($queue1);

        $shift2 = new Shift();
        $shift2->setId(Uuid::v4());
        $shift2->setAgent($agent2);
        $shift2->setStart(new DateTime('2025-05-03 09:00:00'));
        $shift2->setEnd(new DateTime('2025-05-03 17:00:00'));
        $shift2->setQueue($queue2);

        $this->entityRepository
            ->method('findShiftsBetweenDates')
            ->with($startDate, $endDate)
            ->willReturn([$shift1, $shift2]);

        $mappedShiftList = $this->mapper::mapArrayToListContract([$shift1, $shift2]);

        $result = $this->repository->findShiftsBetweenDates($startDate, $endDate);

        $this->assertInstanceOf(ShiftListContract::class, $result);
        $this->assertEquals($mappedShiftList, $result);
    }

    public function testUpsert(): void
    {
        $queueRead = new QueueRead(Uuid::v4(), 'Queue');
        $queueList = new QueueList();
        $queueList->addItem($queueRead);
        $shiftCreateContract = new ShiftCreate(
            Uuid::v4(),
            new AgentRead(Uuid::v4(), 'Agent', $queueList),
            $queueRead,
            new DateTime('2025-05-25 17:00:00'),
            new DateTime('2025-05-25 18:00:00'),
        );

        $shift = $this->mapper::mapCreateContractToEntity($shiftCreateContract);

        $this->entityRepository
            ->expects($this->once())
            ->method('upsert')
            ->with($shift);

        $this->repository->upsert($shiftCreateContract);
    }
}
