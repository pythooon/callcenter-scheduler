<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Repository\ShiftEntityRepository;
use App\Scheduler\Application\Repository\ShiftRepository;
use App\Scheduler\Domain\Mapper\ShiftMapper;

final readonly class ShiftRepositoryImpl implements ShiftRepository
{
    public function __construct(private ShiftEntityRepository $entityRepository, private ShiftMapper $mapper)
    {
    }

    public function findAll(): ShiftListContract
    {
        return $this->mapper::mapArrayToListContract($this->entityRepository->findAll());
    }

    public function upsert(ShiftCreateContract $contract): void
    {
        $this->entityRepository->upsert($this->mapper::mapCreateContractToEntity($contract));
    }
}
