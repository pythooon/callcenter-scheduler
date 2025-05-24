<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final readonly class PredictionRead implements PredictionReadContract
{
    public function __construct(
        private Uuid $id,
        private QueueReadContract $queue,
        private DateTimeInterface $date,
        private DateTimeInterface $time,
        private int $occupancy
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getQueue(): QueueReadContract
    {
        return $this->queue;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getTime(): DateTimeInterface
    {
        return $this->time;
    }

    public function getOccupancy(): int
    {
        return $this->occupancy;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'queue' => $this->queue->toArray(),
            'date' => $this->date->format('Y-m-d'),
            'time' => $this->time->format('H:i'),
            'occupancy' => $this->occupancy,
        ];
    }
}
