<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Mapper;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Application\Contract\ShiftCreateContract;
use App\Scheduler\Application\Contract\ShiftListContract;
use App\Scheduler\Application\Contract\ShiftReadContract;
use App\Scheduler\Domain\Entity\Shift;
use App\Scheduler\Domain\Model\ShiftCreate;
use App\Scheduler\Domain\Model\ShiftList;
use App\Scheduler\Domain\Model\ShiftRead;
use DateTime;
use Symfony\Component\Uid\Uuid;

final readonly class ShiftMapper
{
    /**
     * @param list<Shift> $items
     */
    public static function mapArrayToListContract(array $items): ShiftListContract
    {
        usort($items, function (Shift $a, Shift $b) {
            return $a->getStart() <=> $b->getStart();
        });

        $list = new ShiftList();
        foreach ($items as $item) {
            $list->addItem(self::mapEntityToReadContract($item));
        }

        return $list;
    }

    public static function mapEntityToReadContract(Shift $entity): ShiftReadContract
    {
        $agentReadContract = AgentMapper::mapEntityToReadContract($entity->getAgent());
        $queueReadContract = QueueMapper::mapEntityToReadContract($entity->getQueue());
        return new ShiftRead(
            id: $entity->getId(),
            agent: $agentReadContract,
            queue: $queueReadContract,
            start: $entity->getStart(),
            end: $entity->getEnd()
        );
    }

    public static function mapEntityToCreateContract(
        AgentReadContract $agentReadContract,
        PredictionReadContract $predictionReadContract
    ): ShiftCreateContract {
        $shiftStart = new DateTime(
            $predictionReadContract->getDate()->format('Y-m-d') . $predictionReadContract->getTime()->format('H:i:s')
        );
        $shiftEnd = (clone $shiftStart)->modify("+1 hours");
        return new ShiftCreate(
            id: Uuid::v4(),
            agent: $agentReadContract,
            queue: $predictionReadContract->getQueue(),
            start: $shiftStart,
            end: $shiftEnd
        );
    }

    public static function mapCreateContractToEntity(ShiftCreateContract $contract): Shift
    {
        $agent = AgentMapper::mapReadContractToEntity($contract->getAgent());
        $queue = QueueMapper::mapReadContractToEntity($contract->getQueue());
        $shiftStart = $contract->getStart();
        $shiftEnd = $contract->getEnd();

        $shift = new Shift();
        $shift->setId($contract->getId());
        $shift->setAgent($agent);
        $shift->setQueue($queue);
        $shift->setStart($shiftStart);
        $shift->setEnd($shiftEnd);

        return $shift;
    }
}
