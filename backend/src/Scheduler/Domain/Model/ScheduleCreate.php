<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\ScheduleCreateContract;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final readonly class ScheduleCreate implements ScheduleCreateContract
{
    public function __construct(
        private ?Uuid $queueId = null,
        private ?DateTimeInterface $startDate = null,
        private ?DateTimeInterface $endDate = null
    ) {
    }

    public function getQueueId(): ?Uuid
    {
        return $this->queueId;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }
}
