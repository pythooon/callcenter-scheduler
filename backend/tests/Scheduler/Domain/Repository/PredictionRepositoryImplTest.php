<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Repository\PredictionEntityRepository;
use App\Scheduler\Domain\Entity\Prediction;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Mapper\PredictionMapper;
use App\Scheduler\Domain\Repository\PredictionRepositoryImpl;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PredictionRepositoryImplTest extends TestCase
{
    private PredictionEntityRepository $entityRepository;
    private PredictionMapper $mapper;
    private PredictionRepositoryImpl $repository;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(PredictionEntityRepository::class);
        $this->mapper = new PredictionMapper();
        $this->repository = new PredictionRepositoryImpl($this->entityRepository, $this->mapper);
    }

    public function testFindAll(): void
    {
        $queue1 = new Queue();
        $queue1->setId(Uuid::v4());
        $queue1->setName('Support Queue');
        $prediction1 = new Prediction();
        $prediction1->setId(Uuid::v4());
        $prediction1->setQueue($queue1);
        $prediction1->setDate(new DateTime('2025-05-26'));
        $prediction1->setTime(new DateTime('2025-05-26 09:00:00'));
        $prediction1->setOccupancy(5);

        $queue2 = new Queue();
        $queue2->setId(Uuid::v4());
        $queue2->setName('Support Queue');
        $prediction2 = new Prediction();
        $prediction2->setId(Uuid::v4());
        $prediction2->setQueue($queue2);
        $prediction2->setDate(new DateTime('2025-05-27'));
        $prediction2->setTime(new DateTime('2025-05-27 10:00:00'));
        $prediction2->setOccupancy(3);

        $this->entityRepository
            ->method('findAll')
            ->willReturn([$prediction1, $prediction2]);

        $mappedPredictionList = $this->mapper::mapArrayToListContract([$prediction1, $prediction2]);

        $result = $this->repository->findAll();

        $this->assertInstanceOf(PredictionListContract::class, $result);
        $this->assertEquals($mappedPredictionList, $result);
    }
}
