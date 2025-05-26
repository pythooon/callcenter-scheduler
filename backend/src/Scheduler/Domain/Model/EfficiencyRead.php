<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\EfficiencyReadContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

final readonly class EfficiencyRead implements EfficiencyReadContract
{
    public function __construct(
        private Uuid $id,
        private AgentReadContract $agent,
        private QueueReadContract $queue,
        private float $score,
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

    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @return array{
     *     id: string,
     *     agent: array{
     *         id: string,
     *         name: string
     *     },
     *     queue: array{
     *         id: string,
     *         name: string
     *     },
     *     score: float,
     *     start: string,
     *     end: string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => (string)$this->id,
            'agent' => $this->agent->toArray(),
            'queue' => $this->queue->toArray(),
            'score' => $this->score,
            'start' => $this->start->format('Y-m-d H:i:s'),
            'end' => $this->end->format('Y-m-d H:i:s'),
        ];
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
