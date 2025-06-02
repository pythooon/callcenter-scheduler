<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryListContract;
use App\Scheduler\Application\Repository\CallHistoryEntityRepository;
use App\Scheduler\Application\Repository\CallHistoryRepository;
use App\Scheduler\Domain\Mapper\CallHistoryMapper;
use Symfony\Component\Uid\Uuid;

final readonly class CallHistoryRepositoryImpl implements CallHistoryRepository
{
    public function __construct(
        private CallHistoryEntityRepository $entityRepository,
        private CallHistoryMapper $mapper
    ) {
    }

    /**
     * @param AgentReadContract $agentReadContract
     * @param list<Uuid> $queueIds
     * @return CallHistoryListContract
     */
    public function findByAgentAndQueues(AgentReadContract $agentReadContract, array $queueIds): CallHistoryListContract
    {
        $items = $this->entityRepository->findByAgentIdAndQueueIds($agentReadContract->getId(), $queueIds);
        return $this->mapper::mapArrayToListContract($items);
    }
}
