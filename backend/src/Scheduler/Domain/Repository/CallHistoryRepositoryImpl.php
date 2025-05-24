<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Repository\CallHistoryEntityRepository;
use App\Scheduler\Application\Repository\CallHistoryRepository;
use App\Scheduler\Domain\Mapper\CallHistoryMapper;

final readonly class CallHistoryRepositoryImpl implements CallHistoryRepository
{
    public function __construct(
        private CallHistoryEntityRepository $entityRepository,
        private CallHistoryMapper $mapper
    ) {
    }

    public function findByAgentReadContract(AgentReadContract $agentReadContract): CallHistoryListContract
    {
        $items = $this->entityRepository->findByAgentId($agentReadContract->getId());
        return $this->mapper::mapArrayToListContract($items);
    }
}
