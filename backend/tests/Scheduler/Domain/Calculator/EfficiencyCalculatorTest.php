<?php

declare(strict_types=1);

namespace Tests\App\Scheduler\Domain\Calculator;

use App\Scheduler\Domain\Calculator\EfficiencyCalculator;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\CallHistoryList;
use App\Scheduler\Domain\Model\CallHistoryRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class EfficiencyCalculatorTest extends TestCase
{
    public function testCalculateReturnsCorrectEfficiency(): void
    {
        $start = new DateTime('2024-01-01 00:00:00');
        $end = new DateTime('2024-01-01 23:59:59');

        $queue = new QueueRead(Uuid::v4(), 'Support');
        $queueList = new QueueList();
        $queueList->addItem($queue);

        $agent = new AgentRead(Uuid::v4(), 'John Doe', $queueList);

        $callHistoryList = new CallHistoryList();
        $callHistoryList->addItem(new CallHistoryRead(
            Uuid::v4(),
            $agent,
            $queue,
            new DateTime('2024-01-01 10:00:00'),
            10
        ));

        $calculator = new EfficiencyCalculator();
        $efficiencyList = $calculator->calculate($agent, $callHistoryList, $start, $end);

        $items = $efficiencyList->getItems();
        $this->assertCount(1, $items);
        $this->assertEquals(10.0, $items[0]->getScore());
        $this->assertEquals($start, $items[0]->getStart());
        $this->assertEquals($end, $items[0]->getEnd());
    }
}
