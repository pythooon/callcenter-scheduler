<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

interface ScheduleCreateContract
{
    public function getQueueId(): ?Uuid;

    public function getStartDate(): ?DateTimeInterface;

    public function getEndDate(): ?DateTimeInterface;
}
