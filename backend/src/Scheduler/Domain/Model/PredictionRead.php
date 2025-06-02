<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\PredictionReadContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final class PredictionRead implements PredictionReadContract
{
    private int $diffOccupancy;

    public function __construct(
        private readonly Uuid $id,
        private readonly QueueReadContract $queue,
        private readonly DateTimeInterface $date,
        private readonly DateTimeInterface $time,
        private readonly int $occupancy
    ) {
        $this->diffOccupancy = $occupancy;
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

    public function diffOccupancy(float $score): int
    {
        $this->diffOccupancy -= (int)floor($score);
        return $this->diffOccupancy;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'queue' => $this->queue->toArray(),
            'dateTime' => $this->date->format('Y-m-d') . ' ' . $this->time->format('H:i'),
            'occupancy' => $this->occupancy,
        ];
    }
}
