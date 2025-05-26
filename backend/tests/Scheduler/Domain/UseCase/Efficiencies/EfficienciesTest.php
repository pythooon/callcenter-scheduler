<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\Efficiencies;

use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\EfficiencyRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\UseCase\Efficiencies\Efficiencies;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EfficienciesTest extends TestCase
{
    private EfficiencyRepository $efficiencyRepository;
    private Efficiencies $efficienciesUseCase;

    protected function setUp(): void
    {
        $this->efficiencyRepository = $this->createMock(EfficiencyRepository::class);
        $this->efficienciesUseCase = new Efficiencies($this->efficiencyRepository);
    }

    public function testRunReturnsEfficiencyList(): void
    {
        $score = 95.5;
        $id = Uuid::v4();
        $queueRead = new QueueRead(Uuid::v4(), 'Queue 1');
        $queueList = new QueueList();
        $queueList->addItem($queueRead);
        $agentRead = new AgentRead(Uuid::v4(), 'Agent 1', $queueList);
        $efficiency = new EfficiencyRead(
            $id,
            $agentRead,
            $queueRead,
            $score,
            new DateTime('2025-04-25 17:00:00'),
            new DateTime('2025-05-25 17:00:00')
        );
        $efficiencyList = new EfficiencyList();
        $efficiencyList->addItem($efficiency);

        $this->efficiencyRepository->method('findAll')->willReturn($efficiencyList);

        $result = $this->efficienciesUseCase->run();

        $this->assertInstanceOf(EfficiencyListContract::class, $result);
        $this->assertGreaterThan(0, count($result->getItems()));
        $this->assertSame($id, $result->getItems()[0]->getId());
        $this->assertSame($score, $result->getItems()[0]->getScore());
    }

    public function testRunReturnsEmptyListWhenNoEfficiencies(): void
    {
        $this->efficiencyRepository->method('findAll')->willReturn(new EfficiencyList());

        $result = $this->efficienciesUseCase->run();

        $this->assertInstanceOf(EfficiencyListContract::class, $result);
        $this->assertCount(0, $result->getItems());
    }
}
