<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Repository\AgentEntityRepository;
use App\Scheduler\Application\Repository\AgentRepository;
use App\Scheduler\Domain\Mapper\AgentMapper;

final readonly class AgentRepositoryImpl implements AgentRepository
{
    public function __construct(
        private AgentEntityRepository $entityRepository,
        private AgentMapper $mapper
    ) {
    }

    public function findAll(): AgentListContract
    {
        return $this->mapper::mapArrayToListContract($this->entityRepository->findAll());
    }
}
