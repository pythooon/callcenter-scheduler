<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Repository\EfficiencyEntityRepository;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\EfficiencyCreate;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Repository\EfficiencyRepositoryImpl;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EfficiencyRepositoryImplTest extends TestCase
{
    private EfficiencyEntityRepository $entityRepository;
    private EfficiencyMapper $mapper;
    private EfficiencyRepositoryImpl $repository;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(EfficiencyEntityRepository::class);
        $this->mapper = new EfficiencyMapper();
        $this->repository = new EfficiencyRepositoryImpl($this->entityRepository, $this->mapper);
    }

    public function testUpsert(): void
    {
        $id = Uuid::v4();
        $queueList = new QueueList();
        $queue = new QueueRead(Uuid::v4(), 'Test Queue');
        $queueList->addItem($queue);
        $agent = new AgentRead(Uuid::v4(), 'Test Agent', $queueList);
        $score = 0.85;
        $start = new DateTime('2024-01-01 09:00:00');
        $end = new DateTime('2024-01-01 17:00:00');

        $contract = new EfficiencyCreate(
            $id,
            $agent,
            $queue,
            $score,
            $start,
            $end
        );

        $entity = $this->mapper::mapCreateContractToEntity($contract);

        $this->entityRepository
            ->expects($this->once())
            ->method('upsert')
            ->with($this->callback(fn($arg) => $arg == $entity));

        $this->repository->upsert($contract);
    }

    public function testFindAll(): void
    {
        $id1 = Uuid::v4();
        $queueList1 = new QueueList();
        $queue1 = new QueueRead(Uuid::v4(), 'Test Queue 1');
        $queueList1->addItem($queue1);
        $agent1 = new AgentRead(Uuid::v4(), 'Test Agent 1', $queueList1);
        $score1 = 0.85;
        $start1 = new DateTime('2024-01-01 09:00:00');
        $end1 = new DateTime('2024-01-01 17:00:00');

        $contract1 = new EfficiencyCreate(
            $id1,
            $agent1,
            $queue1,
            $score1,
            $start1,
            $end1
        );
        $id2 = Uuid::v4();
        $queueList2 = new QueueList();
        $queue2 = new QueueRead(Uuid::v4(), 'Test Queue 2');
        $queueList2->addItem($queue1);
        $agent2 = new AgentRead(Uuid::v4(), 'Test Agent 2', $queueList2);
        $score2 = 0.85;
        $start2 = new DateTime('2024-01-02 09:00:00');
        $end2 = new DateTime('2024-01-02 17:00:00');

        $contract2 = new EfficiencyCreate(
            $id2,
            $agent2,
            $queue2,
            $score2,
            $start2,
            $end2
        );
        $entity1 = $this->mapper::mapCreateContractToEntity($contract1);
        $entity2 = $this->mapper::mapCreateContractToEntity($contract2);

        $entities = [$entity1, $entity2];

        $this->entityRepository
            ->method('findAll')
            ->willReturn($entities);

        $result = $this->repository->findAll();

        $this->assertInstanceOf(EfficiencyList::class, $result);
        $this->assertCount(2, $result->getItems());
    }
}
