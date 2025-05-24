<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

interface PredictionReadContract extends Arrayable
{
    public function getId(): Uuid;

    public function getQueue(): QueueReadContract;

    public function getDate(): DateTimeInterface;

    public function getTime(): DateTimeInterface;

    public function getOccupancy(): int;
}
