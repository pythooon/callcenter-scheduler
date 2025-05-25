<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Mapper;

use App\Scheduler\Domain\Mapper\AgentMapper;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AgentMapperTest extends TestCase
{
    private Agent $agentEntity;
    private AgentReadContract $agentReadContract;

    protected function setUp(): void
    {
        $agentId = Uuid::v4();
        $this->agentEntity = new Agent();
        $this->agentEntity->setId($agentId);
        $this->agentEntity->setName('Test Agent');

        $queue1 = new Queue();
        $queue1->setId(Uuid::v4());
        $queue1->setName('Queue 1');
        $queue2 = new Queue();
        $queue2->setId(Uuid::v4());
        $queue2->setName('Queue 2');

        $this->agentEntity->addQueue($queue1);
        $this->agentEntity->addQueue($queue2);

        $queueList = new QueueList();
        $queueList->addItem(new QueueRead($queue1->getId(), $queue1->getName()));
        $queueList->addItem(new QueueRead($queue2->getId(), $queue2->getName()));

        $this->agentReadContract = new AgentRead(
            id: $agentId,
            name: 'Test Agent',
            queues: $queueList,
        );
    }

    public function testMapEntityToReadContract(): void
    {
        $mappedContract = AgentMapper::mapEntityToReadContract($this->agentEntity);

        $this->assertEquals($this->agentReadContract->getId(), $mappedContract->getId());
        $this->assertEquals($this->agentReadContract->getName(), $mappedContract->getName());
        $this->assertCount(2, $mappedContract->getQueues()->getItems());
    }

    public function testMapArrayToListContract(): void
    {
        $agents = [$this->agentEntity];
        $mappedList = AgentMapper::mapArrayToListContract($agents);

        $this->assertCount(1, $mappedList->getItems());
        $this->assertEquals($this->agentReadContract, $mappedList->getItems()[0]);
    }

    public function testMapReadContractToEntity(): void
    {
        $mappedEntity = AgentMapper::mapReadContractToEntity($this->agentReadContract);

        $this->assertEquals($this->agentEntity->getId(), $mappedEntity->getId());
        $this->assertEquals($this->agentEntity->getName(), $mappedEntity->getName());
        $this->assertCount(2, $mappedEntity->getQueues());
    }
}
