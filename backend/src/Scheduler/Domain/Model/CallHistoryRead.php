<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\CallHistoryReadContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use Symfony\Component\Uid\Uuid;
use DateTimeInterface;

final readonly class CallHistoryRead implements CallHistoryReadContract
{
    public function __construct(
        private Uuid $id,
        private AgentReadContract $agent,
        private QueueReadContract $queue,
        private DateTimeInterface $date,
        private int $callsCount
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

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getCallsCount(): int
    {
        return $this->callsCount;
    }
}
