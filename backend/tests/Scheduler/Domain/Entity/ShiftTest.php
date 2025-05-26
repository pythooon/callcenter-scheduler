<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Entity;

use App\Scheduler\Domain\Entity\Shift;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ShiftTest extends TestCase
{
    private Shift $shift;
    private Agent $agent;
    private Queue $queue;
    private \DateTime $start;
    private \DateTime $end;
    private Uuid $uuid;

    protected function setUp(): void
    {
        $this->shift = new Shift();
        $this->agent = $this->createMock(Agent::class);
        $this->queue = $this->createMock(Queue::class);
        $this->start = new \DateTime('2025-05-26 09:00:00');
        $this->end = new \DateTime('2025-05-26 17:00:00');
        $this->uuid = Uuid::v4();

        $this->shift->setId($this->uuid);
        $this->shift->setAgent($this->agent);
        $this->shift->setQueue($this->queue);
        $this->shift->setStart($this->start);
        $this->shift->setEnd($this->end);
    }

    public function testGetId(): void
    {
        $this->assertEquals($this->uuid, $this->shift->getId());
    }

    public function testSetId(): void
    {
        $newId = Uuid::v4();
        $this->shift->setId($newId);
        $this->assertEquals($newId, $this->shift->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->shift->getAgent());
    }

    public function testSetAgent(): void
    {
        $newAgent = $this->createMock(Agent::class);
        $this->shift->setAgent($newAgent);
        $this->assertSame($newAgent, $this->shift->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->shift->getQueue());
    }

    public function testSetQueue(): void
    {
        $newQueue = $this->createMock(Queue::class);
        $this->shift->setQueue($newQueue);
        $this->assertSame($newQueue, $this->shift->getQueue());
    }

    public function testGetStart(): void
    {
        $this->assertEquals($this->start, $this->shift->getStart());
    }

    public function testSetStart(): void
    {
        $newStart = new \DateTime('2025-05-27 08:00:00');
        $this->shift->setStart($newStart);
        $this->assertEquals($newStart, $this->shift->getStart());
    }

    public function testGetEnd(): void
    {
        $this->assertEquals($this->end, $this->shift->getEnd());
    }

    public function testSetEnd(): void
    {
        $newEnd = new \DateTime('2025-05-27 16:00:00');
        $this->shift->setEnd($newEnd);
        $this->assertEquals($newEnd, $this->shift->getEnd());
    }
}
