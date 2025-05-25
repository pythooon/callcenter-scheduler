<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\CallHistoryList;
use App\Scheduler\Domain\Model\CallHistoryRead;
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AgentReadTest extends TestCase
{
    private Uuid $uuid;
    private string $name;
    private QueueList $queues;
    private AgentRead $agentRead;

    protected function setUp(): void
    {
        $this->uuid = Uuid::v4();
        $this->name = 'Test Agent';

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
        $efficiencyRead = new EfficiencyRead(Uuid::v4(), $this->agentRead, $queue, 85.5);

        $this->agentRead->addEfficiency($efficiencyRead);

        $this->assertCount(1, $this->agentRead->getEfficiencyListContract()->getItems());
        $this->assertSame($efficiencyRead, $this->agentRead->getEfficiencyListContract()->getItems()[0]);
    }

    public function testGetScore(): void
    {
        $queue = $this->queues->getItems()[0];

        $efficiencyRead1 = new EfficiencyRead(Uuid::v4(), $this->agentRead, $queue, 80.0);
        $efficiencyRead2 = new EfficiencyRead(Uuid::v4(), $this->agentRead, $queue, 90.0);

        $this->agentRead->addEfficiency($efficiencyRead1);
        $this->agentRead->addEfficiency($efficiencyRead2);

        $score = $this->agentRead->getScore($queue->getId());

        $this->assertEquals(80.0, $score);
    }

    public function testCalculateEfficiency(): void
    {
        $queue1 = $this->queues->getItems()[0];
        $queue2 = $this->queues->getItems()[1];

        $callHistory1 = new CallHistoryRead(Uuid::v4(), $this->agentRead, $queue1, new \DateTime('2023-05-25'), 10);
        $callHistory2 = new CallHistoryRead(Uuid::v4(), $this->agentRead, $queue1, new \DateTime('2023-05-25'), 20);
        $callHistory3 = new CallHistoryRead(Uuid::v4(), $this->agentRead, $queue2, new \DateTime('2023-05-25'), 30);

        $callHistoryList = new CallHistoryList();
        $callHistoryList->addItem($callHistory1);
        $callHistoryList->addItem($callHistory2);
        $callHistoryList->addItem($callHistory3);

        $this->agentRead->calculateEfficiency($callHistoryList);

        $efficiencyList = $this->agentRead->getEfficiencyListContract();
        $this->assertCount(2, $efficiencyList->getItems());

        $efficiencyRead1 = $efficiencyList->getItems()[0];
        $efficiencyRead2 = $efficiencyList->getItems()[1];

        $this->assertEquals(30.0, $efficiencyRead1->getScore());
        $this->assertEquals(30.0, $efficiencyRead2->getScore());
    }

    public function testToArray(): void
    {
        $result = $this->agentRead->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
    }
}
