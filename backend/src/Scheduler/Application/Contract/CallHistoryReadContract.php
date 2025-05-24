<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use Symfony\Component\Uid\Uuid;
use DateTimeInterface;

interface CallHistoryReadContract
{
    public function getId(): Uuid;

    public function getAgent(): AgentReadContract;

    public function getQueue(): QueueReadContract;

    public function getDate(): DateTimeInterface;

    public function getCallsCount(): int;
}
