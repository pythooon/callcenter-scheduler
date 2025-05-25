<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Domain\Model\AgentList;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Application\Contract\AgentReadContract;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;

class AgentListTest extends TestCase
{
    private AgentList $agentList;

    protected function setUp(): void
    {
        $this->agentList = new AgentList();
    }

    public function testAddItem(): void
    {
        $uuid = Uuid::v4();
        $name = 'Test Agent';
        $agentRead = $this->createMock(AgentReadContract::class);

        $this->agentList->addItem($agentRead);

        $items = $this->agentList->getItems();

        $this->assertCount(1, $items);
        $this->assertSame($agentRead, $items[0]);
    }

    public function testGetItems(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();
        $name1 = 'Agent 1';
        $name2 = 'Agent 2';

        $agentRead1 = new AgentRead($uuid1, $name1, $this->createMock(QueueListContract::class));
        $agentRead2 = new AgentRead($uuid2, $name2, $this->createMock(QueueListContract::class));

        $this->agentList->addItem($agentRead1);
        $this->agentList->addItem($agentRead2);

        $items = $this->agentList->getItems();

        $this->assertCount(2, $items);
        $this->assertSame($agentRead1, $items[0]);
        $this->assertSame($agentRead2, $items[1]);
    }

    public function testToArray(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();
        $name1 = 'Agent 1';
        $name2 = 'Agent 2';

        $agentRead1 = new AgentRead($uuid1, $name1, $this->createMock(QueueListContract::class));
        $agentRead2 = new AgentRead($uuid2, $name2, $this->createMock(QueueListContract::class));

        $this->agentList->addItem($agentRead1);
        $this->agentList->addItem($agentRead2);

        $result = $this->agentList->toArray();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertSame((string)$uuid1, $result[0]['id']);
        $this->assertSame($name1, $result[0]['name']);

        $this->assertArrayHasKey('id', $result[1]);
        $this->assertArrayHasKey('name', $result[1]);
        $this->assertSame((string)$uuid2, $result[1]['id']);
        $this->assertSame($name2, $result[1]['name']);
    }
}
