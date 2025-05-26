<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Entity;

use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Efficiency;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EfficiencyTest extends TestCase
{
    private Efficiency $efficiency;
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

        $this->efficiency = new Efficiency();
        $this->efficiency->setId(Uuid::v4());
        $this->efficiency->setAgent($this->agent);
        $this->efficiency->setQueue($this->queue);
        $this->efficiency->setScore(95.5);
    }

    public function testGetId(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->efficiency->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->efficiency->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->efficiency->getQueue());
    }

    public function testGetScore(): void
    {
        $this->assertEquals(95.5, $this->efficiency->getScore());
    }

    public function testSetAgent(): void
    {
        $newAgent = new Agent();
        $newAgent->setId(Uuid::v4());
        $newAgent->setName('New Agent');

        $this->efficiency->setAgent($newAgent);
        $this->assertSame($newAgent, $this->efficiency->getAgent());
    }

    public function testSetQueue(): void
    {
        $newQueue = new Queue();
        $newQueue->setId(Uuid::v4());
        $newQueue->setName('New Queue');
        $this->efficiency->setQueue($newQueue);
        $this->assertSame($newQueue, $this->efficiency->getQueue());
    }

    public function testSetScore(): void
    {
        $newScore = 89.7;
        $this->efficiency->setScore($newScore);
        $this->assertEquals($newScore, $this->efficiency->getScore());
    }

    public function testSetId(): void
    {
        $newId = Uuid::v4();
        $this->efficiency->setId($newId);
        $this->assertEquals($newId, $this->efficiency->getId());
    }
}
