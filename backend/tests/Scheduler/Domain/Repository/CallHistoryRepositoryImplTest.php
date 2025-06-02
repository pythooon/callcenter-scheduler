<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Repository\CallHistoryEntityRepository;
use App\Scheduler\Domain\Mapper\CallHistoryMapper;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Repository\CallHistoryRepositoryImpl;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class CallHistoryRepositoryImplTest extends TestCase
{
    private CallHistoryEntityRepository $entityRepository;
    private CallHistoryMapper $mapper;
    private CallHistoryRepositoryImpl $repository;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(CallHistoryEntityRepository::class);
        $this->mapper = new CallHistoryMapper();
        $this->repository = new CallHistoryRepositoryImpl($this->entityRepository, $this->mapper);
    }

    public function testFindByAgentReadContract(): void
    {
        $id = Uuid::v4();
        $queueId = Uuid::v4();
        $queueList = new QueueList();
        $queueList->addItem(new QueueRead($queueId, 'Queue 1'));
        $agentReadContract = new AgentRead($id, 'Agent 1', $queueList);;

        $callHistoryItems = [];

        $this->entityRepository
            ->method('findByAgentIdAndQueueIds')
            ->with($id, [$queueId])
            ->willReturn($callHistoryItems);

        $callHistoryListContract = $this->mapper::mapArrayToListContract($callHistoryItems);

        $result = $this->repository->findByAgentAndQueues($agentReadContract, [$queueId]);

        $this->assertEquals($callHistoryListContract, $result);
    }
}
