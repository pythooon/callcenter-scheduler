<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\Shifts;

use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Shift;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\ShiftList;
use App\Scheduler\Domain\Model\ShiftRead;
use App\Scheduler\Domain\UseCase\Shifts\Shifts;
use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Repository\ShiftRepository;
use PHPUnit\Framework\TestCase;
use DateTime;
use Symfony\Component\Uid\Uuid;

final class ShiftsTest extends TestCase
{
    public function testRunReturnsShiftListContract(): void
    {
        $start = new DateTime('2024-01-01 00:00:00');
        $end = new DateTime('2024-01-31 23:59:59');
        $queueRead = new QueueRead(Uuid::v4(), 'Test Queue');
        $queueList = new QueueList();
        $queueList->addItem($queueRead);
        $agentRead = new AgentRead(Uuid::v4(), 'Test Agent', $queueList);;
        $shiftRead = new ShiftRead(Uuid::v4(), $agentRead, $queueRead, $start, $end);
        $shiftList = new ShiftList();
        $shiftList->addItem($shiftRead);

        $repositoryMock = $this->createMock(ShiftRepository::class);
        $repositoryMock
            ->expects($this->once())
            ->method('findShiftsBetweenDates')
            ->with($start, $end)
            ->willReturn($shiftList);

        $useCase = new Shifts($repositoryMock);

        $result = $useCase->run($start, $end);

        $this->assertSame($shiftList, $result);
    }
}
