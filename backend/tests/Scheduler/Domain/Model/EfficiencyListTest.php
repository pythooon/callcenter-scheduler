<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use DateTime;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;

class EfficiencyListTest extends TestCase
{
    private EfficiencyList $efficiencyList;
    private Uuid $uuid;
    private AgentRead $agentRead;
    private QueueRead $queueRead;
    private EfficiencyRead $efficiencyRead;

    protected function setUp(): void
    {
        $this->efficiencyList = new EfficiencyList();
        $this->uuid = Uuid::v4();

        $this->queueRead = new QueueRead(Uuid::v4(), 'Test Queue');
        $queueList = new QueueList();
        $queueList->addItem($this->queueRead);
        $this->agentRead = new AgentRead($this->uuid, 'Test Agent', $queueList);

        $this->efficiencyRead = new EfficiencyRead(
            $this->uuid,
            $this->agentRead,
            $this->queueRead,
            95.5
        );
    }

    public function testAddItem(): void
    {
        $this->efficiencyList->addItem($this->efficiencyRead);

        $this->assertCount(1, $this->efficiencyList->getItems());
        $this->assertSame($this->efficiencyRead, $this->efficiencyList->getItems()[0]);
    }

    public function testGetItems(): void
    {
        $this->efficiencyList->addItem($this->efficiencyRead);

        $items = $this->efficiencyList->getItems();

        $this->assertCount(1, $items);
        $this->assertSame($this->efficiencyRead, $items[0]);
    }

    public function testToArray(): void
    {
        $this->efficiencyList->addItem($this->efficiencyRead);

        $result = $this->efficiencyList->toArray();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('agent', $result[0]);
        $this->assertArrayHasKey('queue', $result[0]);
        $this->assertArrayHasKey('score', $result[0]);
        $this->assertIsArray($result[0]['agent']);
        $this->assertIsArray($result[0]['queue']);
    }
}
