<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\CallHistoryRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CallHistoryReadTest extends TestCase
{
    private Uuid $id;
    private AgentRead $agent;
    private QueueRead $queue;
    private DateTime $date;
    private int $callsCount;
    private CallHistoryRead $callHistoryRead;

    protected function setUp(): void
    {
        $this->id = Uuid::v4();
        $this->queue = new QueueRead($this->id, 'Queue Name');
        $queueList = new QueueList();
        $queueList->addItem($this->queue);
        $this->agent = new AgentRead($this->id, 'Agent Name', $queueList);
        $this->date = new DateTime('2023-05-25');
        $this->callsCount = 100;

        $this->callHistoryRead = new CallHistoryRead(
            $this->id,
            $this->agent,
            $this->queue,
            $this->date,
            $this->callsCount
        );
    }

    public function testGetId(): void
    {
        $this->assertSame($this->id, $this->callHistoryRead->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->callHistoryRead->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->callHistoryRead->getQueue());
    }

    public function testGetDate(): void
    {
        $this->assertSame($this->date, $this->callHistoryRead->getDate());
    }

    public function testGetCallsCount(): void
    {
        $this->assertSame($this->callsCount, $this->callHistoryRead->getCallsCount());
    }
}
