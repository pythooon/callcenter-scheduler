<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\EfficiencyCreateContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use Symfony\Component\Uid\Uuid;

class EfficiencyCreate implements EfficiencyCreateContract
{
    public function __construct(
        private Uuid $id,
        private AgentReadContract $agent,
        private QueueReadContract $queue,
        private float $score
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

    public function getScore(): float
    {
        return $this->score;
    }
}
