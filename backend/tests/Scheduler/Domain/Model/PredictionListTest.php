<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\PredictionList;
use App\Scheduler\Domain\Model\PredictionRead;
use App\Scheduler\Domain\Model\QueueRead;
use DateTime;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;

class PredictionListTest extends TestCase
{
    private PredictionList $predictionList;
    private Uuid $uuid;
    private QueueRead $queue;
    private PredictionRead $predictionRead;

    protected function setUp(): void
    {
        $this->predictionList = new PredictionList();
        $this->uuid = Uuid::v4();
        $this->queue = new QueueRead(Uuid::v4(), 'Test Queue');
        $date = new DateTime('2023-05-25');
        $time = new DateTime('2023-05-25 09:00:00');
        $this->predictionRead = new PredictionRead(
            $this->uuid,
            $this->queue,
            $date,
            $time,
            75
        );
    }

    public function testAddItem(): void
    {
        $this->predictionList->addItem($this->predictionRead);

        $this->assertCount(1, $this->predictionList->getItems());
        $this->assertSame($this->predictionRead, $this->predictionList->getItems()[0]);
    }

    public function testGetItems(): void
    {
        $this->predictionList->addItem($this->predictionRead);

        $items = $this->predictionList->getItems();

        $this->assertCount(1, $items);
        $this->assertSame($this->predictionRead, $items[0]);
    }

    public function testToArray(): void
    {
        $this->predictionList->addItem($this->predictionRead);

        $result = $this->predictionList->toArray();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('queue', $result[0]);
        $this->assertArrayHasKey('dateTime', $result[0]);
        $this->assertArrayHasKey('occupancy', $result[0]);
    }
}
