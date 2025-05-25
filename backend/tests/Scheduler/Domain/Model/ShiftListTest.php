<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\ShiftList;
use App\Scheduler\Domain\Model\ShiftRead;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueRead;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ShiftListTest extends TestCase
{
    private ShiftList $shiftList;
    private Uuid $id;
    private AgentRead $agent;
    private QueueRead $queue;
    private DateTime $start;
    private DateTime $end;
    private ShiftRead $shiftRead;

    protected function setUp(): void
    {
        $this->id = Uuid::v4();
        $this->queue = new QueueRead(Uuid::v4(), 'Test Queue');
        $queueList = new QueueList();
        $queueList->addItem($this->queue);
        $this->agent = new AgentRead($this->id, 'Test Agent', $queueList);
        $this->start = new DateTime('2023-05-25 09:00:00');
        $this->end = new DateTime('2023-05-25 17:00:00');
        $this->shiftRead = new ShiftRead($this->id, $this->agent, $this->queue, $this->start, $this->end);
        $this->shiftList = new ShiftList();
    }

    public function testAddItem(): void
    {
        $this->shiftList->addItem($this->shiftRead);
        $this->assertCount(1, $this->shiftList->getItems());
        $this->assertSame($this->shiftRead, $this->shiftList->getItems()[0]);
    }

    public function testToArray(): void
    {
        $this->shiftList->addItem($this->shiftRead);
        $result = $this->shiftList->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('agent', $result[0]);
        $this->assertArrayHasKey('queue', $result[0]);
        $this->assertArrayHasKey('start', $result[0]);
        $this->assertArrayHasKey('end', $result[0]);
    }
}
