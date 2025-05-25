<?php

declare(strict_types=1);

namespace App\Tests\Mapper;

use App\Scheduler\Domain\Entity\Prediction;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Mapper\PredictionMapper;
use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Application\Contract\PredictionListContract;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use DateTime;

class PredictionMapperTest extends TestCase
{
    public function testMapEntityToReadContract(): void
    {
        $queue = new Queue();
        $queue->setId(Uuid::v4());
        $queue->setName('Test Queue');
        $prediction = new Prediction();
        $prediction->setId(Uuid::v4());
        $prediction->setQueue($queue);
        $prediction->setDate(new DateTime('2023-06-01'));
        $prediction->setTime(new DateTime('2023-06-01 08:00:00'));
        $prediction->setOccupancy(80);

        $predictionReadContract = PredictionMapper::mapEntityToReadContract($prediction);

        $this->assertInstanceOf(PredictionReadContract::class, $predictionReadContract);
        $this->assertEquals($prediction->getId(), $predictionReadContract->getId());
        $this->assertEquals($prediction->getDate(), $predictionReadContract->getDate());
        $this->assertEquals($prediction->getTime(), $predictionReadContract->getTime());
        $this->assertEquals($prediction->getOccupancy(), $predictionReadContract->getOccupancy());
    }

    public function testMapArrayToListContract(): void
    {
        $prediction1 = new Prediction();
        $prediction1->setId(Uuid::v4());
        $prediction1->setDate(new DateTime('2023-06-01'));
        $prediction1->setOccupancy(70);
        $prediction1->setTime(new DateTime('2023-06-01 08:00:00'));
        $queue1 = new Queue();
        $queue1->setId(Uuid::v4());
        $queue1->setName('Queue 1');
        $prediction1->setQueue($queue1);

        $prediction2 = new Prediction();
        $prediction2->setId(Uuid::v4());
        $prediction2->setDate(new DateTime('2023-06-02'));
        $prediction2->setOccupancy(90);
        $prediction2->setTime(new DateTime('2023-06-02 08:00:00'));
        $queue2 = new Queue();
        $queue2->setId(Uuid::v4());
        $queue2->setName('Queue 2');
        $prediction2->setQueue($queue2);

        $predictionArray = [$prediction2, $prediction1];

        $predictionListContract = PredictionMapper::mapArrayToListContract($predictionArray);

        $this->assertInstanceOf(PredictionListContract::class, $predictionListContract);
        $this->assertCount(2, $predictionListContract->getItems());
        $this->assertEquals($prediction1->getId(), $predictionListContract->getItems()[1]->getId());
        $this->assertEquals($prediction2->getId(), $predictionListContract->getItems()[0]->getId());
    }
}
