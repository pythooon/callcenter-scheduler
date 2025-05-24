<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\Efficiencies;

use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Repository\EfficiencyRepository;

final readonly class Efficiencies
{
    public function __construct(private EfficiencyRepository $repository)
    {
    }

    public function run(): EfficiencyListContract
    {
        return $this->repository->findAll();
    }
}
