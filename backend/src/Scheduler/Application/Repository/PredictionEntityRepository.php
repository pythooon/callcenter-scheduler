<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\Prediction;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

interface PredictionEntityRepository
{
    /**
     * @return list<Prediction>
     */
    public function findPredictionByDateAndTime(
        Uuid $queueId,
        \DateTimeInterface $date,
        \DateTimeInterface $time
    ): array;

    /**
     * @return list<Prediction>
     */
    public function findAll(): array;

    /**
     * @return list<Prediction>
     */
    public function findByStartAndEndDate(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null,
        ?Uuid $queueId = null
    ): array;
}
