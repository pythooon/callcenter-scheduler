<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\Shifts;

use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Repository\ShiftRepository;

final readonly class Shifts
{
    public function __construct(private ShiftRepository $repository)
    {
    }

    public function run(): ShiftListContract
    {
        return $this->repository->findAll();
    }
}
