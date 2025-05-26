<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\EfficiencyCreate;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\QueueList;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EfficiencyCreateTest extends TestCase
{
    private EfficiencyCreate $efficiencyCreate;
    private Uuid $id;
    private AgentRead $agent;
    private QueueRead $queue;
    private float $score;
    private DateTime $start;
    private DateTime $end;

    protected function setUp(): void
    {
        $this->id = Uuid::v4();
        $queueList = new QueueList();
        $this->queue = new QueueRead(Uuid::v4(), 'Test Queue');
        $queueList->addItem($this->queue);
        $this->agent = new AgentRead(Uuid::v4(), 'Test Agent', $queueList);
        $this->score = 0.85;
        $this->start = new DateTime('2024-01-01 09:00:00');
        $this->end = new DateTime('2024-01-01 17:00:00');

        $this->efficiencyCreate = new EfficiencyCreate(
            $this->id,
            $this->agent,
            $this->queue,
            $this->score,
            $this->start,
            $this->end
        );
    }

    public function testGetId(): void
    {
        $this->assertEquals($this->id, $this->efficiencyCreate->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->efficiencyCreate->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->efficiencyCreate->getQueue());
    }

    public function testGetScore(): void
    {
        $this->assertEquals($this->score, $this->efficiencyCreate->getScore());
    }

    public function testGetStart(): void
    {
        $this->assertEquals($this->start, $this->efficiencyCreate->getStart());
    }

    public function testSetStart(): void
    {
        $newStart = new DateTime('2024-01-02 09:00:00');
        $this->efficiencyCreate->setStart($newStart);
        $this->assertEquals($newStart, $this->efficiencyCreate->getStart());
    }

    public function testGetEnd(): void
    {
        $this->assertEquals($this->end, $this->efficiencyCreate->getEnd());
    }

    public function testSetEnd(): void
    {
        $newEnd = new DateTime('2024-01-02 17:00:00');
        $this->efficiencyCreate->setEnd($newEnd);
        $this->assertEquals($newEnd, $this->efficiencyCreate->getEnd());
    }
}