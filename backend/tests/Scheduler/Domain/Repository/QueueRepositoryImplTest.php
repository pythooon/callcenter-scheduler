<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Repository\QueueEntityRepository;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Mapper\QueueMapper;
use App\Scheduler\Domain\Repository\QueueRepositoryImpl;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class QueueRepositoryImplTest extends TestCase
{
    private QueueEntityRepository $entityRepository;
    private QueueMapper $mapper;
    private QueueRepositoryImpl $repository;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(QueueEntityRepository::class);
        $this->mapper = new QueueMapper();
        $this->repository = new QueueRepositoryImpl($this->entityRepository, $this->mapper);
    }

    public function testFindAll(): void
    {
        $queue1 = new Queue();
        $queue1->setId(Uuid::v4());
        $queue1->setName('Queue 1');

        $queue2 = new Queue();
        $queue2->setId(Uuid::v4());
        $queue2->setName('Queue 2');

        $this->entityRepository
            ->method('findAll')
            ->willReturn([$queue1, $queue2]);

        $mappedQueueList = $this->mapper::mapArrayToListContract([$queue1, $queue2]);

        $result = $this->repository->findAll();

        $this->assertInstanceOf(QueueListContract::class, $result);
        $this->assertEquals($mappedQueueList, $result);
    }
}
