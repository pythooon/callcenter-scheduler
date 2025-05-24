<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use App\Scheduler\Application\Contract\ShiftCreateContract;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final readonly class ShiftCreate implements ShiftCreateContract
{
    public function __construct(
        private Uuid $id,
        private AgentReadContract $agent,
        private QueueReadContract $queue,
        private DateTimeInterface $start,
        private DateTimeInterface $end,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAgent(): AgentReadContract
    {
        return $this->agent;
    }

    public function getQueue(): QueueReadContract
    {
        return $this->queue;
    }

    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }
}
