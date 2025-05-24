<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\Predictions;

use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Repository\PredictionRepository;

final readonly class Predictions
{
    public function __construct(private PredictionRepository $repository)
    {
    }

    public function run(): PredictionListContract
    {
        return $this->repository->findAll();
    }
}
