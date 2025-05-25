<?php

declare(strict_types=1);

namespace App\Tests\Mapper;

use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Contract\ShiftReadContract;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Shift;
use App\Scheduler\Domain\Mapper\ShiftMapper;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\PredictionRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\Model\ShiftCreate;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ShiftMapperTest extends TestCase
{
    public function testMapEntityToReadContract(): void
    {
        $agent = new Agent();
        $agent->setId(Uuid::v4());
        $agent->setName('Agent A');

        $queue = new Queue();
        $queue->setId(Uuid::v4());
        $queue->setName('Queue A');

        $shift = new Shift();
        $shift->setId(Uuid::v4());
        $shift->setAgent($agent);
        $shift->setQueue($queue);
        $shift->setStart(new DateTime('2023-06-01 08:00:00'));
        $shift->setEnd(new DateTime('2023-06-01 16:00:00'));

        $shiftReadContract = ShiftMapper::mapEntityToReadContract($shift);

        $this->assertInstanceOf(ShiftReadContract::class, $shiftReadContract);
        $this->assertEquals($shift->getId(), $shiftReadContract->getId());
        $this->assertEquals($shift->getStart(), $shiftReadContract->getStart());
        $this->assertEquals($shift->getEnd(), $shiftReadContract->getEnd());
    }

    public function testMapEntityToCreateContract(): void
    {
        $queue = new QueueRead(Uuid::v4(), 'Queue A');
        $queueList = new QueueList();
        $queueList->addItem($queue);
        $agent = new AgentRead(Uuid::v4(), 'Agent A', $queueList);;

        $predictionReadContract = new PredictionRead(Uuid::v4(), $queue, new DateTime('2023-06-01'), new DateTime('2024-06-01 16:00'), 100);;

        $shiftCreateContract = ShiftMapper::mapEntityToCreateContract($agent, $predictionReadContract);

        $this->assertInstanceOf(ShiftCreateContract::class, $shiftCreateContract);
        $this->assertNotNull($shiftCreateContract->getStart());
        $this->assertNotNull($shiftCreateContract->getEnd());
        $this->assertEquals($predictionReadContract->getQueue(), $shiftCreateContract->getQueue());
    }

    public function testMapCreateContractToEntity(): void
    {
        $queueList = new QueueList();
        $queue = new QueueRead(Uuid::v4(), 'Queue A');;
        $queueList->addItem($queue);
        $agent = new AgentRead(Uuid::v4(), 'Agent A', $queueList);;

        $shiftCreateContract = new ShiftCreate(
            id: Uuid::v4(),
            agent: $agent,
            queue: $queue,
            start: new DateTime('2023-06-01 08:00:00'),
            end: new DateTime('2023-06-01 16:00:00')
        );

        $shiftEntity = ShiftMapper::mapCreateContractToEntity($shiftCreateContract);

        $this->assertInstanceOf(Shift::class, $shiftEntity);
        $this->assertEquals($shiftCreateContract->getId(), $shiftEntity->getId());
        $this->assertEquals($shiftCreateContract->getStart(), $shiftEntity->getStart());
        $this->assertEquals($shiftCreateContract->getEnd(), $shiftEntity->getEnd());
    }

    public function testMapArrayToListContract(): void
    {
        $agent1 = new Agent();
        $agent1->setId(Uuid::v4());
        $agent1->setName('Agent A');

        $queue1 = new Queue();
        $queue1->setId(Uuid::v4());
        $queue1->setName('Queue A');

        $shift1 = new Shift();
        $shift1->setId(Uuid::v4());
        $shift1->setAgent($agent1);
        $shift1->setQueue($queue1);
        $shift1->setStart(new DateTime('2023-06-01 08:00:00'));
        $shift1->setEnd(new DateTime('2023-06-01 16:00:00'));

        $agent2 = new Agent();
        $agent2->setId(Uuid::v4());
        $agent2->setName('Agent B');

        $queue2 = new Queue();
        $queue2->setId(Uuid::v4());
        $queue2->setName('Queue B');

        $shift2 = new Shift();
        $shift2->setId(Uuid::v4());
        $shift2->setAgent($agent2);
        $shift2->setQueue($queue2);
        $shift2->setStart(new DateTime('2023-06-02 08:00:00'));
        $shift2->setEnd(new DateTime('2023-06-02 16:00:00'));

        $shiftArray = [$shift2, $shift1];

        $shiftListContract = ShiftMapper::mapArrayToListContract($shiftArray);

        $this->assertInstanceOf(ShiftListContract::class, $shiftListContract);
        $this->assertCount(2, $shiftListContract->getItems());
        $this->assertEquals($shift1->getStart(), $shiftListContract->getItems()[0]->getStart());
        $this->assertEquals($shift2->getStart(), $shiftListContract->getItems()[1]->getStart());
    }
}
