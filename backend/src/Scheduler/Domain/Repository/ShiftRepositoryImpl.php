<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Repository\ShiftEntityRepository;
use App\Scheduler\Application\Repository\ShiftRepository;
use App\Scheduler\Domain\Mapper\ShiftMapper;
use DateTimeInterface;

final readonly class ShiftRepositoryImpl implements ShiftRepository
{
    public function __construct(private ShiftEntityRepository $entityRepository, private ShiftMapper $mapper)
    {
    }

    public function findShiftsBetweenDates(?DateTimeInterface $start, ?DateTimeInterface $end): ShiftListContract
    {
        return $this->mapper::mapArrayToListContract($this->entityRepository->findShiftsBetweenDates($start, $end));
    }

    public function upsert(ShiftCreateContract $contract): void
    {
        $this->entityRepository->upsert($this->mapper::mapCreateContractToEntity($contract));
    }
}
