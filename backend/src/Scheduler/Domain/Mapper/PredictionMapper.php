<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Mapper;

use App\Scheduler\Application\Contract\PredictionListContract;
use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Domain\Entity\Prediction;
use App\Scheduler\Domain\Model\PredictionList;
use App\Scheduler\Domain\Model\PredictionRead;

final readonly class PredictionMapper
{
    /**
     * @param list<Prediction> $items
     */
    public static function mapArrayToListContract(array $items): PredictionListContract
    {
        $list = new PredictionList();
        foreach ($items as $item) {
            $list->addItem(self::mapEntityToReadContract($item));
        }
        return $list;
    }

    public static function mapEntityToReadContract(Prediction $prediction): PredictionReadContract
    {
        return new PredictionRead(
            id: $prediction->getId(),
            queue: QueueMapper::mapEntityToReadContract($prediction->getQueue()),
            date: $prediction->getDate(),
            time: $prediction->getTime(),
            occupancy: $prediction->getOccupancy()
        );
    }
}
