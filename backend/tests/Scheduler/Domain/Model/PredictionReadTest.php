<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\PredictionRead;
use App\Scheduler\Domain\Model\QueueRead;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;
use DateTime;

class PredictionReadTest extends TestCase
{
    private Uuid $uuid;
    private QueueRead $queueRead;
    private DateTime $date;
    private DateTime $time;
    private PredictionRead $predictionRead;

    protected function setUp(): void
    {
        $this->uuid = Uuid::v4();
        $this->queueRead = new QueueRead($this->uuid, 'Test Queue');
        $this->date = new DateTime('2023-05-25');
        $this->time = new DateTime('12:00:00');
        $this->predictionRead = new PredictionRead(
            $this->uuid,
            $this->queueRead,
            $this->date,
            $this->time,
            85
        );
    }

    public function testGetId(): void
    {
        $this->assertSame($this->uuid, $this->predictionRead->getId());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queueRead, $this->predictionRead->getQueue());
    }

    public function testGetDate(): void
    {
        $this->assertEquals($this->date, $this->predictionRead->getDate());
    }

    public function testGetTime(): void
    {
        $this->assertEquals($this->time, $this->predictionRead->getTime());
    }

    public function testGetOccupancy(): void
    {
        $this->assertEquals(85, $this->predictionRead->getOccupancy());
    }

    public function testDiffOccupancy(): void
    {
        $score = 10.5;
        $this->assertEquals(75, $this->predictionRead->diffOccupancy($score));
    }

    public function testToArray(): void
    {
        $result = $this->predictionRead->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('queue', $result);
        $this->assertArrayHasKey('dateTime', $result);
        $this->assertArrayHasKey('occupancy', $result);

        $this->assertIsArray($result['queue']);
        $this->assertEquals($this->date->format('Y-m-d') .' '. $this->time->format('H:i'), $result['dateTime']);
    }
}
