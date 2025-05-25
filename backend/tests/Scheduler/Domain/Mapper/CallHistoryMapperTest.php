<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Mapper;

use App\Scheduler\Domain\Entity\CallHistory;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Mapper\CallHistoryMapper;
use App\Scheduler\Domain\Model\CallHistoryRead;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Application\Contract\CallHistoryReadContract;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use DateTime;

class CallHistoryMapperTest extends TestCase
{
    private CallHistory $callHistory;
    private CallHistoryReadContract $callHistoryReadContract;

    protected function setUp(): void
    {
        $id = Uuid::v4();
        $agent = new Agent();
        $agent->setId($id);
        $agent->setName('Test Agent');
        $queue = new Queue();
        $queue->setId($id);
        $queue->setName('Test Queue');
        $agent->addQueue($queue);
        $date = new DateTime('2025-05-25');
        $callsCount = 100;

        $this->callHistory = new CallHistory();
        $this->callHistory->setId($id);
        $this->callHistory->setAgent($agent);
        $this->callHistory->setQueue($queue);
        $this->callHistory->setDate($date);
        $this->callHistory->setCallsCount($callsCount);

        $queueRead = new QueueRead($queue->getId(), 'Test Queue');
        $queueList = new QueueList();
        $queueList->addItem($queueRead);
        $agentRead = new AgentRead($agent->getId(), 'Test Agent', $queueList);;

        $this->callHistoryReadContract = new CallHistoryRead(
            id: $id,
            agent: $agentRead,
            queue: $queueRead,
            date: $date,
            callsCount: $callsCount
        );
    }

    public function testMapEntityToReadContract(): void
    {
        $mappedContract = CallHistoryMapper::mapEntityToReadContract($this->callHistory);

        $this->assertEquals($this->callHistoryReadContract->getId(), $mappedContract->getId());
        $this->assertEquals($this->callHistoryReadContract->getAgent(), $mappedContract->getAgent());
        $this->assertEquals($this->callHistoryReadContract->getQueue(), $mappedContract->getQueue());
        $this->assertEquals($this->callHistoryReadContract->getDate(), $mappedContract->getDate());
        $this->assertEquals($this->callHistoryReadContract->getCallsCount(), $mappedContract->getCallsCount());
    }

    public function testMapArrayToListContract(): void
    {
        $callHistories = [$this->callHistory];
        $mappedList = CallHistoryMapper::mapArrayToListContract($callHistories);

        $this->assertCount(1, $mappedList->getItems());
        $this->assertEquals($this->callHistoryReadContract, $mappedList->getItems()[0]);
    }
}
