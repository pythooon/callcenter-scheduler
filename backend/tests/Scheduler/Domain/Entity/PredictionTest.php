<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Entity;

use App\Scheduler\Domain\Entity\Prediction;
use App\Scheduler\Domain\Entity\Queue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PredictionTest extends TestCase
{
    private Prediction $prediction;
    private Queue $queue;

    protected function setUp(): void
    {
        $this->queue = new Queue();
        $this->queue->setId(Uuid::v4());
        $this->queue->setName('Test Queue');
        $this->prediction = new Prediction();
        $this->prediction->setId(Uuid::v4());
        $this->prediction->setQueue($this->queue);
        $this->prediction->setDate(new \DateTime('2025-06-01'));
        $this->prediction->setTime(new \DateTime('2025-06-01 14:00:00'));
        $this->prediction->setOccupancy(85);
    }

    public function testGetId(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->prediction->getId());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->prediction->getQueue());
    }

    public function testGetDate(): void
    {
        $this->assertEquals(new \DateTime('2025-06-01'), $this->prediction->getDate());
    }

    public function testGetTime(): void
    {
        $this->assertEquals(new \DateTime('2025-06-01 14:00:00'), $this->prediction->getTime());
    }

    public function testGetOccupancy(): void
    {
        $this->assertEquals(85, $this->prediction->getOccupancy());
    }

    public function testSetQueue(): void
    {
        $newQueue = new Queue(Uuid::v4(), 'New Queue');
        $this->prediction->setQueue($newQueue);
        $this->assertSame($newQueue, $this->prediction->getQueue());
    }

    public function testSetDate(): void
    {
        $newDate = new \DateTime('2025-07-01');
        $this->prediction->setDate($newDate);
        $this->assertEquals($newDate, $this->prediction->getDate());
    }

    public function testSetTime(): void
    {
        $newTime = new \DateTime('2025-07-01 10:00:00');
        $this->prediction->setTime($newTime);
        $this->assertEquals($newTime, $this->prediction->getTime());
    }

    public function testSetOccupancy(): void
    {
        $this->prediction->setOccupancy(90);
        $this->assertEquals(90, $this->prediction->getOccupancy());
    }

    public function testSetId(): void
    {
        $newId = Uuid::v4();
        $this->prediction->setId($newId);
        $this->assertEquals($newId, $this->prediction->getId());
    }
}
