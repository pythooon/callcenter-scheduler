<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Repository\CallHistoryEntityRepository;
use App\Scheduler\Domain\Mapper\CallHistoryMapper;
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
        $agentReadContract = $this->createMock(AgentReadContract::class);
        $agentReadContract->method('getId')->willReturn($id);

        $callHistoryItems = [];

        $this->entityRepository
            ->method('findByAgentId')
            ->with($id)
            ->willReturn($callHistoryItems);

        $callHistoryListContract = $this->mapper::mapArrayToListContract($callHistoryItems);

        $result = $this->repository->findByAgentReadContract($agentReadContract);

        $this->assertEquals($callHistoryListContract, $result);
    }
}
