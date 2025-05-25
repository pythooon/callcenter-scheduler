<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Entity;

use App\Scheduler\Domain\Entity\CallHistory;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use DateTime;

class CallHistoryTest extends TestCase
{
    private CallHistory $callHistory;
    private Agent $agent;
    private Queue $queue;

    protected function setUp(): void
    {
        $this->agent = new Agent();
        $this->agent->setId(Uuid::v4());
        $this->agent->setName('Test Agent');

        $this->queue = new Queue();
        $this->queue->setId(Uuid::v4());
        $this->queue->setName('Test Queue');

        $this->callHistory = new CallHistory();
        $this->callHistory->setId(Uuid::v4());
        $this->callHistory->setAgent($this->agent);
        $this->callHistory->setQueue($this->queue);
        $this->callHistory->setDate(new DateTime('2025-05-25 12:00:00'));
        $this->callHistory->setCallsCount(10);
    }

    public function testGetId(): void
    {
        $this->assertNotNull($this->callHistory->getId());
        $this->assertInstanceOf(Uuid::class, $this->callHistory->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->callHistory->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->callHistory->getQueue());
    }

    public function testGetDate(): void
    {
        $this->assertEquals(new DateTime('2025-05-25 12:00:00'), $this->callHistory->getDate());
    }

    public function testGetCallsCount(): void
    {
        $this->assertEquals(10, $this->callHistory->getCallsCount());
    }

    public function testSetAndGetId(): void
    {
        $newId = Uuid::v4();
        $this->callHistory->setId($newId);

        $this->assertSame($newId, $this->callHistory->getId());
    }

    public function testSetAndGetAgent(): void
    {
        $newAgent = new Agent();
        $newAgent->setId(Uuid::v4());
        $newAgent->setName('New Agent');
        $this->callHistory->setAgent($newAgent);

        $this->assertSame($newAgent, $this->callHistory->getAgent());
    }

    public function testSetAndGetQueue(): void
    {
        $newQueue = new Queue();
        $newQueue->setId(Uuid::v4());
        $newQueue->setName('New Queue');
        $this->callHistory->setQueue($newQueue);

        $this->assertSame($newQueue, $this->callHistory->getQueue());
    }

    public function testSetAndGetDate(): void
    {
        $newDate = new DateTime('2025-06-01 15:00:00');
        $this->callHistory->setDate($newDate);

        $this->assertEquals($newDate, $this->callHistory->getDate());
    }

    public function testSetAndGetCallsCount(): void
    {
        $this->callHistory->setCallsCount(20);
        $this->assertEquals(20, $this->callHistory->getCallsCount());
    }
}
