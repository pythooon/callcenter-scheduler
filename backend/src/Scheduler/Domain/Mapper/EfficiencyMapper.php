<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Mapper;

use App\Scheduler\Application\Contract\EfficiencyCreateContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Contract\EfficiencyReadContract;
use App\Scheduler\Domain\Entity\Efficiency;
use App\Scheduler\Domain\Model\EfficiencyCreate;
use App\Scheduler\Domain\Model\EfficiencyList;
use App\Scheduler\Domain\Model\EfficiencyRead;

final readonly class EfficiencyMapper
{
    /**
     * @param list<Efficiency> $items
     */
    public static function mapArrayToListContract(array $items): EfficiencyListContract
    {
        $queueList = new EfficiencyList();
        foreach ($items as $item) {
            $queueList->addItem(self::mapEntityToReadContract($item));
        }

        return $queueList;
    }

    public static function mapEntityToReadContract(Efficiency $efficiency): EfficiencyReadContract
    {
        $agentReadContract = AgentMapper::mapEntityToReadContract($efficiency->getAgent());
        $queueReadContract = QueueMapper::mapEntityToReadContract($efficiency->getQueue());
        return new EfficiencyRead(
            id: $efficiency->getId(),
            agent: $agentReadContract,
            queue: $queueReadContract,
            score: $efficiency->getScore()
        );
    }

    public static function mapReadContractToCreateContract(EfficiencyReadContract $contract): EfficiencyCreateContract
    {
        return new EfficiencyCreate(
            id: $contract->getId(),
            agent: $contract->getAgent(),
            queue: $contract->getQueue(),
            score: $contract->getScore()
        );
    }

    public static function mapCreateContractToEntity(EfficiencyCreateContract $contract): Efficiency
    {
        $agentEntity = AgentMapper::mapReadContractToEntity($contract->getAgent());
        $queueEntity = QueueMapper::mapReadContractToEntity($contract->getQueue());

        $efficiency = new Efficiency();
        $efficiency->setId($contract->getId());
        $efficiency->setAgent($agentEntity);
        $efficiency->setQueue($queueEntity);
        $efficiency->setScore($contract->getScore());

        return $efficiency;
    }
}
