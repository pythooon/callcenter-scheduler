<?php

declare(strict_types=1);

namespace App\Tests\Mapper;

use App\Scheduler\Domain\Entity\Efficiency;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;
use App\Scheduler\Application\Contract\EfficiencyReadContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class EfficiencyMapperTest extends TestCase
{
    public function testMapEntityToReadContract(): void
    {
        $agent = new Agent();
        $agent->setId(Uuid::v4());
        $agent->setName('Test Agent');
        $queue = new Queue();
        $queue->setId(Uuid::v4());
        $queue->setName('Test Queue');
        $efficiency = new Efficiency();
        $efficiency->setId(Uuid::v4());
        $efficiency->setAgent($agent);
        $efficiency->setQueue($queue);
        $efficiency->setScore(90.5);
        $efficiency->setStart(new DateTime('2025-04-25 17:00:00'));
        $efficiency->setEnd(new DateTime('2025-05-25 17:00:00'));

        $efficiencyReadContract = EfficiencyMapper::mapEntityToReadContract($efficiency);

        $this->assertInstanceOf(EfficiencyReadContract::class, $efficiencyReadContract);
        $this->assertEquals($efficiency->getId(), $efficiencyReadContract->getId());
        $this->assertEquals($efficiency->getScore(), $efficiencyReadContract->getScore());
    }

    public function testMapArrayToListContract(): void
    {
        $efficiency1 = new Efficiency();
        $efficiency1->setId(Uuid::v4());
        $efficiency1->setScore(80.5);
        $efficiency1->setStart(new DateTime('2025-04-25 17:00:00'));
        $efficiency1->setEnd(new DateTime('2025-05-25 17:00:00'));
        $queue1 = new Queue();
        $queue1->setId(Uuid::v4());
        $queue1->setName('Queue 1');
        $agent1 = new Agent();
        $agent1->setId(Uuid::v4());
        $agent1->setName('Agent 1');
        $efficiency1->setAgent($agent1);
        $efficiency1->setQueue($queue1);

        $efficiency2 = new Efficiency();
        $efficiency2->setId(Uuid::v4());
        $efficiency2->setScore(95.5);
        $efficiency2->setStart(new DateTime('2025-04-25 17:00:00'));
        $efficiency2->setEnd(new DateTime('2025-05-25 17:00:00'));
        $queue2 = new Queue();
        $queue2->setId(Uuid::v4());
        $queue2->setName('Queue 2');
        $agent2 = new Agent();
        $agent2->setId(Uuid::v4());
        $agent2->setName('Agent 2');
        $efficiency2->setAgent($agent2);
        $efficiency2->setQueue($queue2);
        $efficiencyArray = [$efficiency2, $efficiency1];

        $efficiencyListContract = EfficiencyMapper::mapArrayToListContract($efficiencyArray);

        $this->assertInstanceOf(EfficiencyListContract::class, $efficiencyListContract);
        $this->assertCount(2, $efficiencyListContract->getItems());
        $this->assertEquals($efficiency1->getId(), $efficiencyListContract->getItems()[1]->getId());
        $this->assertEquals($efficiency2->getId(), $efficiencyListContract->getItems()[0]->getId());
    }
}
