<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\Agents;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Repository\AgentRepository;

final readonly class Agents
{
    public function __construct(private AgentRepository $repository)
    {
    }

    public function run(): AgentListContract
    {
        return $this->repository->findAll();
    }
}
