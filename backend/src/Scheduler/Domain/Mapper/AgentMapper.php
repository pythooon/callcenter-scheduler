<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Mapper;

use App\Scheduler\Application\Contract\AgentListContract;
use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Model\AgentList;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;

final readonly class AgentMapper
{
    /**
     * @param list<Agent> $items
     */
    public static function mapArrayToListContract(array $items): AgentListContract
    {
        $list = new AgentList();
        foreach ($items as $item) {
            $list->addItem(self::mapEntityToReadContract($item));
        }
        return $list;
    }

    public static function mapEntityToReadContract(Agent $agentEntity): AgentReadContract
    {
        $queueList = new QueueList();
        foreach ($agentEntity->getQueues() as $queue) {
            $queueReadContract = QueueMapper::mapEntityToReadContract($queue);
            $queueList->addItem($queueReadContract);
        };
        return new AgentRead(
            id: $agentEntity->getId(),
            name: $agentEntity->getName(),
            queues: $queueList,
        );
    }

    public static function mapReadContractToEntity(AgentReadContract $contract): Agent
    {
        $agent = new Agent();
        $agent->setId($contract->getId());
        $agent->setName($contract->getName());

        foreach ($contract->getQueues()->getItems() as $queueReadContract) {
            $queueEntity = QueueMapper::mapReadContractToEntity($queueReadContract);
            $agent->addQueue($queueEntity);
        }

        return $agent;
    }
}
