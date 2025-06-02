<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Request;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class ScheduleGenerateRequest
{
    #[Assert\Date]
    public ?string $start_date = null;

    #[Assert\Date]
    public ?string $end_date = null;

    #[Assert\Uuid]
    public ?string $queue_id = null;

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->start_date !== null ? new DateTime($this->start_date) : null;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->end_date !== null ? new DateTime($this->end_date) : null;
    }

    public function getQueueId(): ?Uuid
    {
        return $this->queue_id !== null ? Uuid::fromString($this->queue_id) : null;
    }
}
