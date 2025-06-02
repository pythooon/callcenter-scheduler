<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\ShiftRead;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueRead;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ShiftReadTest extends TestCase
{
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
    }

    public function testGetId(): void
    {
        $this->assertEquals($this->id, $this->shiftRead->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->shiftRead->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->shiftRead->getQueue());
    }

    public function testGetStart(): void
    {
        $this->assertEquals($this->start, $this->shiftRead->getStart());
    }

    public function testGetEnd(): void
    {
        $this->assertEquals($this->end, $this->shiftRead->getEnd());
    }

    public function testToArray(): void
    {
        $result = $this->shiftRead->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('agent', $result);
        $this->assertArrayHasKey('queue', $result);
        $this->assertArrayHasKey('start', $result);
        $this->assertArrayHasKey('end', $result);

        $this->assertEquals((string) $this->id, $result['id']);
        $agentArray = $this->agent->toArray();
        unset($agentArray['queues']);
        $this->assertEquals($agentArray, $result['agent']);
        $this->assertEquals($this->queue->toArray(), $result['queue']);
        $this->assertEquals($this->start->format('Y-m-d H:i:s'), $result['start']);
        $this->assertEquals($this->end->format('Y-m-d H:i:s'), $result['end']);
    }
}
