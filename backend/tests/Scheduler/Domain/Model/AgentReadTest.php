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

class AgentReadTest extends TestCase
{
    private Uuid $uuid;
    private string $name;
    private float $score;
    private QueueList $queues;
    private AgentRead $agentRead;

    protected function setUp(): void
    {
        $this->uuid = Uuid::v4();
        $this->name = 'Test Agent';
        $this->score = 85.5;

        $queue1 = new QueueRead(Uuid::v4(), 'Queue 1');
        $queue2 = new QueueRead(Uuid::v4(), 'Queue 2');
        $this->queues = new QueueList();
        $this->queues->addItem($queue1);
        $this->queues->addItem($queue2);

        $this->agentRead = new AgentRead($this->uuid, $this->name, $this->queues);
    }

    public function testGetId(): void
    {
        $this->assertSame($this->uuid, $this->agentRead->getId());
    }

    public function testGetName(): void
    {
        $this->assertSame($this->name, $this->agentRead->getName());
    }

    public function testGetQueues(): void
    {
        $this->assertSame($this->queues, $this->agentRead->getQueues());
    }

    public function testAddEfficiency(): void
    {
        $queue = $this->queues->getItems()[0];
        $start = new DateTime('2024-01-01 00:00:00');
        $end = new DateTime('2024-01-01 23:59:59');

        $efficiencyRead = new EfficiencyRead(
            Uuid::v4(),
            $this->agentRead,
            $queue,
            $this->score,
            $start,
            $end
        );

        $this->agentRead->addEfficiency($efficiencyRead);

        $this->assertCount(1, $this->agentRead->getEfficiencyListContract()->getItems());
        $this->assertSame($efficiencyRead, $this->agentRead->getEfficiencyListContract()->getItems()[0]);
    }

    public function testGetScore(): void
    {
        $queue = $this->queues->getItems()[0];
        $start = new DateTime('2024-01-01 00:00:00');
        $end = new DateTime('2024-01-01 23:59:59');

        $efficiencyRead1 = new EfficiencyRead(
            Uuid::v4(),
            $this->agentRead,
            $queue,
            80.0,
            $start,
            $end
        );
        $efficiencyRead2 = new EfficiencyRead(
            Uuid::v4(),
            $this->agentRead,
            $queue,
            90.0,
            $start,
            $end
        );

        $this->agentRead->addEfficiency($efficiencyRead1);
        $this->agentRead->addEfficiency($efficiencyRead2);

        $score = $this->agentRead->getScore($queue->getId());

        $this->assertEquals(80.0, $score);
    }

    public function testToArray(): void
    {
        $result = $this->agentRead->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
    }
}
