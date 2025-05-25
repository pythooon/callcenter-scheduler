<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\EfficiencyCreateContract;
use App\Scheduler\Application\Repository\EfficiencyEntityRepository;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Repository\EfficiencyRepositoryImpl;
use PHPUnit\Framework\TestCase;

class EfficiencyRepositoryImplTest extends TestCase
{
    private EfficiencyEntityRepository $entityRepository;
    private EfficiencyMapper $mapper;
    private EfficiencyRepositoryImpl $repository;

    protected function setUp(): void
    {
        $this->entityRepository = $this->createMock(EfficiencyEntityRepository::class);
        $this->mapper = new EfficiencyMapper();
        $this->repository = new EfficiencyRepositoryImpl($this->entityRepository, $this->mapper);
    }

    public function testUpsert(): void
    {
        $contract = $this->createMock(EfficiencyCreateContract::class);

        $entity = $this->mapper::mapCreateContractToEntity($contract);

        $this->entityRepository
            ->expects($this->once())
            ->method('upsert')
            ->with($this->callback(fn($arg) => $arg == $entity));

        $this->repository->upsert($contract);
    }

    public function testFindAll(): void
    {
        $entity1 = $this->mapper::mapCreateContractToEntity($this->createMock(EfficiencyCreateContract::class));
        $entity2 = $this->mapper::mapCreateContractToEntity($this->createMock(EfficiencyCreateContract::class));

        $entities = [$entity1, $entity2];

        $this->entityRepository
            ->method('findAll')
            ->willReturn($entities);

        $result = $this->repository->findAll();

        $this->assertInstanceOf(EfficiencyList::class, $result);
        $this->assertCount(2, $result->getItems());
    }
}
