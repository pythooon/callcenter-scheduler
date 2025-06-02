<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Repository\PredictionEntityRepository;
use App\Scheduler\Application\Repository\PredictionRepository;
use App\Scheduler\Domain\Mapper\PredictionMapper;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final readonly class PredictionRepositoryImpl implements PredictionRepository
{
    public function __construct(private PredictionEntityRepository $entityRepository, private PredictionMapper $mapper)
    {
    }

    public function findAll(): PredictionListContract
    {
        return $this->mapper::mapArrayToListContract($this->entityRepository->findAll());
    }

    public function findByStartAndEndDate(
        ?DateTimeInterface $startDate = null,
        ?DateTimeInterface $endDate = null,
        ?Uuid $queueId = null
    ): PredictionListContract {
        return $this->mapper::mapArrayToListContract(
            $this->entityRepository->findByStartAndEndDate($startDate, $endDate, $queueId)
        );
    }
}
