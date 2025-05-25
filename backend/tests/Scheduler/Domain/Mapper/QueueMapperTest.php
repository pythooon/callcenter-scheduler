<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Mapper;

use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;

final readonly class QueueMapper
{
    public static function mapArrayToListContract(array $items): QueueListContract
    {
        $queueList = new QueueList();
        foreach ($items as $item) {
            $queueList->addItem(self::mapEntityToReadContract($item));
        }

        return $queueList;
    }

    public static function mapEntityToReadContract(Queue $queueEntity): QueueReadContract
    {
        return new QueueRead(
            id: $queueEntity->getId(),
            name: $queueEntity->getName(),
        );
    }

    public static function mapReadContractToEntity(QueueReadContract $contract): Queue
    {
        $queue = new Queue();
        $queue->setId($contract->getId());
        $queue->setName($contract->getName());

        return $queue;
    }
}
