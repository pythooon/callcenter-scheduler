<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\CallHistoryList;
use App\Scheduler\Domain\Model\CallHistoryRead;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\QueueList;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;
use DateTime;

class CallHistoryListTest extends TestCase
{
    private CallHistoryList $callHistoryList;

    protected function setUp(): void
    {
        $this->callHistoryList = new CallHistoryList();
    }

    public function testAddItem(): void
    {
        $queueList = new QueueList();
        $queueRead = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueList->addItem($queueRead);

        $callHistoryRead = new CallHistoryRead(
            Uuid::v4(),
            new AgentRead(Uuid::v4(), 'Agent 1', $queueList),
            $queueRead,
            new DateTime('2023-05-25'),
            10
        );

        $this->callHistoryList->addItem($callHistoryRead);

        $this->assertCount(1, $this->callHistoryList->getItems());
        $this->assertSame($callHistoryRead, $this->callHistoryList->getItems()[0]);
    }

    public function testGetItems(): void
    {
        $queueList1 = new QueueList();
        $queueRead1 = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueList1->addItem($queueRead1);

        $queueList2 = new QueueList();
        $queueRead2 = new QueueRead(Uuid::v4(), 'Queue 2');
        $queueList2->addItem($queueRead2);

        $callHistoryRead1 = new CallHistoryRead(
            Uuid::v4(),
            new AgentRead(Uuid::v4(), 'Agent 1', $queueList1),
            $queueRead1,
            new DateTime('2023-05-25'),
            10
        );

        $callHistoryRead2 = new CallHistoryRead(
            Uuid::v4(),
            new AgentRead(Uuid::v4(), 'Agent 2', $queueList2),
            $queueRead2,
            new DateTime('2023-05-26'),
            20
        );

        $this->callHistoryList->addItem($callHistoryRead1);
        $this->callHistoryList->addItem($callHistoryRead2);

        $items = $this->callHistoryList->getItems();

        $this->assertCount(2, $items);
        $this->assertSame($callHistoryRead1, $items[0]);
        $this->assertSame($callHistoryRead2, $items[1]);
    }

    public function testToArray(): void
    {
        $queueList = new QueueList();
        $queueRead = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueList->addItem($queueRead);

        $callHistoryRead1 = new CallHistoryRead(
            Uuid::v4(),
            new AgentRead(Uuid::v4(), 'Agent 1', $queueList),
            $queueRead,
            new DateTime('2023-05-25'),
            10
        );

        $this->callHistoryList->addItem($callHistoryRead1);

        $result = $this->callHistoryList->toArray();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($callHistoryRead1, $result[0]);
    }
}
