<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\ShiftCreate;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ShiftCreateTest extends TestCase
{
    private Uuid $id;
    private AgentRead $agent;
    private QueueRead $queue;
    private DateTime $start;
    private DateTime $end;
    private ShiftCreate $shiftCreate;

    protected function setUp(): void
    {
        $this->id = Uuid::v4();
        $this->queue = new QueueRead(Uuid::v4(), 'Test Queue');
        $queueList = new QueueList();
        $queueList->addItem($this->queue);
        $this->agent = new AgentRead($this->id, 'Test Agent', $queueList);
        $this->start = new DateTime('2023-05-25 09:00:00');
        $this->end = new DateTime('2023-05-25 17:00:00');

        $this->shiftCreate = new ShiftCreate($this->id, $this->agent, $this->queue, $this->start, $this->end);
    }

    public function testGetId(): void
    {
        $this->assertSame($this->id, $this->shiftCreate->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->shiftCreate->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->shiftCreate->getQueue());
    }

    public function testGetStart(): void
    {
        $this->assertSame($this->start, $this->shiftCreate->getStart());
    }

    public function testGetEnd(): void
    {
        $this->assertSame($this->end, $this->shiftCreate->getEnd());
    }
}
