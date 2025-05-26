<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use DateTime;

class EfficiencyReadTest extends TestCase
{
    private Uuid $uuid;
    private AgentRead $agentRead;
    private QueueRead $queueRead;
    private EfficiencyRead $efficiencyRead;
    private DateTime $start;
    private DateTime $end;
    private float $score;

    protected function setUp(): void
    {
        $this->uuid = Uuid::v4();
        $this->queueRead = new QueueRead($this->uuid, 'Test Queue');

        $queueList = new QueueList();
        $queueList->addItem($this->queueRead);

        $this->agentRead = new AgentRead($this->uuid, 'Test Agent', $queueList);

        $this->start = new DateTime('2024-01-01 00:00:00');
        $this->end = new DateTime('2024-01-07 23:59:59');
        $this->score = 85.5;

        $this->efficiencyRead = new EfficiencyRead(
            $this->uuid,
            $this->agentRead,
            $this->queueRead,
            $this->score,
            $this->start,
            $this->end
        );
    }

    public function testGetId(): void
    {
        $this->assertSame($this->uuid, $this->efficiencyRead->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agentRead, $this->efficiencyRead->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queueRead, $this->efficiencyRead->getQueue());
    }

    public function testGetScore(): void
    {
        $this->assertEquals($this->score, $this->efficiencyRead->getScore());
    }

    public function testGetStartAndEnd(): void
    {
        $this->assertSame($this->start, $this->efficiencyRead->getStart());
        $this->assertSame($this->end, $this->efficiencyRead->getEnd());
    }

    public function testToArray(): void
    {
        $result = $this->efficiencyRead->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('agent', $result);
        $this->assertArrayHasKey('queue', $result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('start', $result);
        $this->assertArrayHasKey('end', $result);

        $this->assertSame((string)$this->uuid, $result['id']);
        $this->assertEquals('Test Agent', $result['agent']['name']);
        $this->assertEquals('Test Queue', $result['queue']['name']);
        $this->assertEquals($this->score, $result['score']);

        $this->assertEquals($this->start->format('Y-m-d H:i:s'), $result['start']);
        $this->assertEquals($this->end->format('Y-m-d H:i:s'), $result['end']);
    }
}
