<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

interface EfficiencyCreateContract
{
    public function getId(): Uuid;

    public function getAgent(): AgentReadContract;

    public function getQueue(): QueueReadContract;

    public function getScore(): float;

    public function getStart(): DateTimeInterface;

    public function getEnd(): DateTimeInterface;
}
