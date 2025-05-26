<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Entity;

use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Shift;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class QueueTest extends TestCase
{
    private Queue $queue;

    protected function setUp(): void
    {
        $this->queue = new Queue();
        $this->queue->setId(Uuid::v4());
        $this->queue->setName('Support Queue');
    }

    public function testGetId(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->queue->getId());
    }

    public function testSetId(): void
    {
        $newId = Uuid::v4();
        $this->queue->setId($newId);
        $this->assertSame($newId, $this->queue->getId());
    }

    public function testGetName(): void
    {
        $this->assertEquals('Support Queue', $this->queue->getName());
    }

    public function testSetName(): void
    {
        $this->queue->setName('Sales Queue');
        $this->assertEquals('Sales Queue', $this->queue->getName());
    }

    public function testGetShiftsInitiallyEmpty(): void
    {
        $this->assertCount(0, $this->queue->getShifts());
    }

    public function testAddShift(): void
    {
        $shift = $this->createMock(Shift::class);

        $this->queue->addShift($shift);

        $this->assertCount(1, $this->queue->getShifts());
        $this->assertTrue($this->queue->getShifts()->contains($shift));
    }

    public function testAddShiftOnlyOnce(): void
    {
        $shift = $this->createMock(Shift::class);

        $this->queue->addShift($shift);
        $this->queue->addShift($shift);

        $this->assertCount(1, $this->queue->getShifts());
    }
}
