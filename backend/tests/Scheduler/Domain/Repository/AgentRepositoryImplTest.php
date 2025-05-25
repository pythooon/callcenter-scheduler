<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Repository\AgentEntityRepository;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Mapper\AgentMapper;
use App\Scheduler\Domain\Repository\AgentRepositoryImpl;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AgentRepositoryImplTest extends TestCase
{
    private AgentEntityRepository $entityRepository;
    private AgentMapper $mapper;
    private AgentRepositoryImpl $repository;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(AgentEntityRepository::class);
        $this->mapper = new AgentMapper();
        $this->repository = new AgentRepositoryImpl($this->entityRepository, $this->mapper);
    }

    public function testFindAll(): void
    {
        $agentData = [];

        $agentOne = new Agent();
        $agentOne->setId(Uuid::v4());
        $agentOne->setName('Agent One');

        $agentTwo = new Agent();
        $agentTwo->setId(Uuid::v4());
        $agentTwo->setName('Agent Two');

        $agentThree = new Agent();
        $agentThree->setId(Uuid::v4());
        $agentThree->setName('Agent Three');

        $agentData = [$agentOne, $agentTwo, $agentThree];

        $this->entityRepository
            ->method('findAll')
            ->willReturn($agentData);

        $mappedAgentList = $this->mapper->mapArrayToListContract($agentData);

        $result = $this->repository->findAll();

        $this->assertEquals($mappedAgentList, $result);
    }
}
