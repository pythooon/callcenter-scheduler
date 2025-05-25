<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class QueueListTest extends TestCase
{
    private QueueList $queueList;

    protected function setUp(): void
    {
        $this->queueList = new QueueList();
    }

    public function testAddItem(): void
    {
        $queueRead = new QueueRead(Uuid::v4(), 'Queue 1');
        $this->queueList->addItem($queueRead);

        $this->assertCount(1, $this->queueList->getItems());
        $this->assertSame($queueRead, $this->queueList->getItems()[0]);
    }

    public function testGetItems(): void
    {
        $queueRead1 = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueRead2 = new QueueRead(Uuid::v4(), 'Queue 2');
        $this->queueList->addItem($queueRead1);
        $this->queueList->addItem($queueRead2);

        $items = $this->queueList->getItems();
        $this->assertCount(2, $items);
        $this->assertSame($queueRead1, $items[0]);
        $this->assertSame($queueRead2, $items[1]);
    }

    public function testToArray(): void
    {
        $queueRead1 = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueRead2 = new QueueRead(Uuid::v4(), 'Queue 2');
        $this->queueList->addItem($queueRead1);
        $this->queueList->addItem($queueRead2);

        $result = $this->queueList->toArray();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($queueRead1, $result[0]);
        $this->assertEquals($queueRead2, $result[1]);
    }
}
