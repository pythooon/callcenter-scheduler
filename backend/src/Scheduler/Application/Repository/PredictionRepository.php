<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Application\Contract\PredictionListContract;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

interface PredictionRepository
{
    public function findAll(): PredictionListContract;

    public function findByStartAndEndDate(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null,
        ?Uuid $queueId = null
    ): PredictionListContract;
}
