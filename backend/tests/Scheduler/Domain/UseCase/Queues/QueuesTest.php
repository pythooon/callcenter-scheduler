<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\Queues;

use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Repository\QueueRepository;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\UseCase\Queues\Queues;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class QueuesTest extends TestCase
{
    private QueueRepository $queueRepository;
    private Queues $queuesUseCase;

    protected function setUp(): void
    {
        $this->queueRepository = $this->createMock(QueueRepository::class);
        $this->queuesUseCase = new Queues($this->queueRepository);
    }

    public function testRunReturnsQueueList(): void
    {
        $id = Uuid::v4();
        $queue = new QueueRead($id, 'Queue 1');
        $queueList = new QueueList();
        $queueList->addItem($queue);
        $this->queueRepository->method('findAll')->willReturn($queueList);

        $result = $this->queuesUseCase->run();

        $this->assertInstanceOf(QueueListContract::class, $result);
        $this->assertGreaterThan(0, count($result->getItems()));
        $this->assertEquals($id, $result->getItems()[0]->getId());
        $this->assertSame('Queue 1', $result->getItems()[0]->getName());
    }

    public function testRunReturnsEmptyListWhenNoQueues(): void
    {
        $this->queueRepository->method('findAll')->willReturn(new QueueList());

        $result = $this->queuesUseCase->run();

        $this->assertInstanceOf(QueueListContract::class, $result);
        $this->assertCount(0, $result->getItems());
    }
}
