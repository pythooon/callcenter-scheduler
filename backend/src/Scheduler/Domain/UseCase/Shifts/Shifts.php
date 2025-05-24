<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\Shifts;

use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Repository\ShiftRepository;
use DateTime;
use DateTimeInterface;

final readonly class Shifts
{
    public function __construct(private ShiftRepository $repository)
    {
    }

    public function run(?DateTimeInterface $start, ?DateTimeInterface $end): ShiftListContract
    {
        return $this->repository->findShiftsBetweenDates($start, $end);
    }
}
