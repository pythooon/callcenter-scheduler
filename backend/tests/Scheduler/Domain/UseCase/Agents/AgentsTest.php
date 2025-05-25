<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\Agents;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Repository\AgentRepository;
use App\Scheduler\Domain\Model\AgentList;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\UseCase\Agents\Agents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class AgentsTest extends TestCase
{
    private AgentRepository $agentRepository;
    private Agents $agentsUseCase;

    protected function setUp(): void
    {
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->agentsUseCase = new Agents($this->agentRepository);
    }

    public function testRunReturnsAgentList(): void
    {
        $id = Uuid::v4();
        $agent = new AgentRead($id, 'Agent 1', new QueueList());
        $agentList = new AgentList();
        $agentList->addItem($agent);
        $this->agentRepository->method('findAll')->willReturn($agentList);

        $result = $this->agentsUseCase->run();

        $this->assertInstanceOf(AgentListContract::class, $result);
        $this->assertGreaterThan(0, count($result->getItems()));
        $this->assertEquals($id, $result->getItems()[0]->getId());
        $this->assertSame('Agent 1', $result->getItems()[0]->getName());
    }

    public function testRunReturnsEmptyListWhenNoAgents(): void
    {
        $this->agentRepository->method('findAll')->willReturn(new AgentList());

        $result = $this->agentsUseCase->run();

        $this->assertInstanceOf(AgentListContract::class, $result);
        $this->assertCount(0, $result->getItems());
    }
}
