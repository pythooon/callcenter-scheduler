<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Model\AgentList;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Repository\AgentRepositoryImpl;
use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Repository\AgentEntityRepository;
use App\Scheduler\Domain\Mapper\AgentMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

final class AgentRepositoryImplTest extends TestCase
{
    private AgentEntityRepository $entityRepositoryMock;
    private AgentMapper $mapper;
    private AgentListContract $agentList;
    private AgentRepositoryImpl $repository;
    private UuidV4 $agentId;
    private string $name;
    private AgentRead $agentRead;

    protected function setUp(): void
    {
        $this->entityRepositoryMock = $this->createMock(AgentEntityRepository::class);
        $this->mapper = new AgentMapper();
        $this->agentId = Uuid::v4();
        $this->name = 'Test Agent';
        $this->agentRead = new AgentRead($this->agentId, $this->name, new QueueList());
        $this->agentList = new AgentList();
        $this->agentList->addItem($this->agentRead);

        $this->repository = new AgentRepositoryImpl($this->entityRepositoryMock, $this->mapper);
    }

    public function testFindAllReturnsAgentListContract(): void
    {
        $agentEntity = new Agent();
        $agentEntity->setId($this->agentId);
        $agentEntity->setName($this->name);

        $agentsArray = [$agentEntity];

        $this->entityRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($agentsArray);

        $result = $this->repository->findAll();

        $this->assertEquals($this->agentList, $result);
    }

    public function testFindByIdsReturnsAgentListContract(): void
    {
        $agentEntity = new Agent();
        $agentEntity->setId($this->agentId);
        $agentEntity->setName($this->name);

        $agentsArray = [$agentEntity];

        $this->entityRepositoryMock
            ->expects($this->once())
            ->method('findByQueueIds')
            ->with([])
            ->willReturn($agentsArray);

        $result = $this->repository->findByQueueIds([]);

        $this->assertEquals($this->agentList, $result);
    }
}
