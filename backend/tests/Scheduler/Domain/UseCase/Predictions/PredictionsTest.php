<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\UseCase\Predictions;

use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Repository\PredictionRepository;
use App\Scheduler\Domain\Model\PredictionList;
use App\Scheduler\Domain\Model\PredictionRead;
use App\Scheduler\Domain\Model\QueueRead;
use App\Scheduler\Domain\UseCase\Predictions\Predictions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PredictionsTest extends TestCase
{
    private PredictionRepository $predictionRepository;
    private Predictions $predictionsUseCase;

    protected function setUp(): void
    {
        $this->predictionRepository = $this->createMock(PredictionRepository::class);
        $this->predictionsUseCase = new Predictions($this->predictionRepository);
    }

    public function testRunReturnsPredictionList(): void
    {
        $queue = new QueueRead(Uuid::v4(), 'Queue 1');
        $id = Uuid::v4();
        $occupancy = 75;
        $prediction = new PredictionRead(
            $id,
            $queue,
            new \DateTime('2025-05-25 16:00:00'),
            new \DateTime('2025-05-25 17:00:00'),
            $occupancy
        );
        $predictionList = new PredictionList();
        $predictionList->addItem($prediction);
        $this->predictionRepository->method('findAll')->willReturn($predictionList);

        $result = $this->predictionsUseCase->run();

        $this->assertInstanceOf(PredictionListContract::class, $result);
        $this->assertGreaterThan(0, count($result->getItems()));
        $this->assertEquals($id, $result->getItems()[0]->getId());
        $this->assertSame($occupancy, $result->getItems()[0]->getOccupancy());
    }

    public function testRunReturnsEmptyListWhenNoPredictions(): void
    {
        $this->predictionRepository->method('findAll')->willReturn(new PredictionList());

        $result = $this->predictionsUseCase->run();

        $this->assertInstanceOf(PredictionListContract::class, $result);
        $this->assertCount(0, $result->getItems());
    }
}
