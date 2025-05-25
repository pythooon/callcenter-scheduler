<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EfficiencyReadTest extends TestCase
{
    private Uuid $uuid;
    private AgentRead $agentRead;
    private QueueRead $queueRead;
    private EfficiencyRead $efficiencyRead;

    protected function setUp(): void
    {
        $this->uuid = Uuid::v4();
        $this->queueRead = new QueueRead($this->uuid, 'Test Queue');
        $queueList = new QueueList();
        $queueList->addItem($this->queueRead);
        $this->agentRead = new AgentRead($this->uuid, 'Test Agent', $queueList);
        $this->efficiencyRead = new EfficiencyRead(
            $this->uuid,
            $this->agentRead,
            $this->queueRead,
            85.5
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
        $this->assertEquals(85.5, $this->efficiencyRead->getScore());
    }

    public function testToArray(): void
    {
        $result = $this->efficiencyRead->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('agent', $result);
        $this->assertArrayHasKey('queue', $result);
        $this->assertArrayHasKey('score', $result);

        $this->assertIsArray($result['agent']);
        $this->assertIsArray($result['queue']);
    }
}
