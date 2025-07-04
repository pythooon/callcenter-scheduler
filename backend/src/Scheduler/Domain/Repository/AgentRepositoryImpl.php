<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Repository\AgentEntityRepository;
use App\Scheduler\Application\Repository\AgentRepository;
use App\Scheduler\Domain\Mapper\AgentMapper;
use Symfony\Component\Uid\Uuid;

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

    /**
     * @param list<Uuid> $ids
     */
    public function findByQueueIds(array $ids = []): AgentListContract
    {
        return $this->mapper::mapArrayToListContract($this->entityRepository->findByQueueIds($ids));
    }
}
