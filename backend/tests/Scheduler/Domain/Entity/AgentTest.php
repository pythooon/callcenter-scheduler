<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Entity;

use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Shift;
use App\Scheduler\Domain\Entity\CallHistory;
use App\Scheduler\Domain\Entity\Efficiency;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AgentTest extends TestCase
{
    private Agent $agent;
    private Queue $queue1;
    private Queue $queue2;
    private Shift $shift;
    private CallHistory $callHistory;
    private Efficiency $efficiency;

    protected function setUp(): void
    {
        $this->agent = new Agent();
        $this->agent->setId(Uuid::v4());
        $this->agent->setName("Test Agent");

        $this->queue1 = new Queue();
        $this->queue1->setId(Uuid::v4());
        $this->queue1->setName("Queue 1");

        $this->queue2 = new Queue();
        $this->queue2->setId(Uuid::v4());
        $this->queue2->setName("Queue 2");

        $this->shift = new Shift();
        $this->shift->setId(Uuid::v4());
        $this->shift->setAgent($this->agent);
        $this->shift->setQueue($this->queue1);
        $this->shift->setStart(new \DateTime('2023-05-01 09:00:00'));
        $this->shift->setEnd(new \DateTime('2023-05-01 17:00:00'));

        $this->callHistory = new CallHistory();
        $this->callHistory->setId(Uuid::v4());
        $this->callHistory->setAgent($this->agent);
        $this->callHistory->setQueue($this->queue1);
        $this->callHistory->setDate(new \DateTime('2023-05-01 09:30:00'));
        $this->callHistory->setCallsCount(5);

        $this->efficiency = new Efficiency();
        $this->efficiency->setId(Uuid::v4());
        $this->efficiency->setAgent($this->agent);
        $this->efficiency->setQueue($this->queue2);
        $this->efficiency->setScore(95.5);
    }

    public function testAddQueue(): void
    {
        $this->agent->addQueue($this->queue1);
        $this->agent->addQueue($this->queue2);

        $this->assertCount(2, $this->agent->getQueues());
        $this->assertTrue($this->agent->getQueues()->contains($this->queue1));
        $this->assertTrue($this->agent->getQueues()->contains($this->queue2));
    }

    public function testAddShift(): void
    {
        $this->agent->addShift($this->shift);

        $this->assertCount(1, $this->agent->getShifts());
        $this->assertTrue($this->agent->getShifts()->contains($this->shift));
    }

    public function testAddCallHistory(): void
    {
        $this->agent->addCallHistory($this->callHistory);

        $this->assertCount(1, $this->agent->getCallHistories());
        $this->assertTrue($this->agent->getCallHistories()->contains($this->callHistory));
    }

    public function testAddEfficiency(): void
    {
        $this->agent->addEfficiency($this->efficiency);

        $this->assertCount(1, $this->agent->getEfficiencies());
        $this->assertTrue($this->agent->getEfficiencies()->contains($this->efficiency));
    }

    public function testGetId(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->agent->getId());
    }

    public function testGetName(): void
    {
        $this->assertEquals("Test Agent", $this->agent->getName());
    }

    public function testGetQueues(): void
    {
        $this->agent->addQueue($this->queue1);
        $this->agent->addQueue($this->queue2);

        $this->assertCount(2, $this->agent->getQueues());
    }

    public function testGetShifts(): void
    {
        $this->agent->addShift($this->shift);
        $this->assertCount(1, $this->agent->getShifts());
    }

    public function testGetCallHistories(): void
    {
        $this->agent->addCallHistory($this->callHistory);
        $this->assertCount(1, $this->agent->getCallHistories());
    }

    public function testGetEfficiencies(): void
    {
        $this->agent->addEfficiency($this->efficiency);
        $this->assertCount(1, $this->agent->getEfficiencies());
    }
}
