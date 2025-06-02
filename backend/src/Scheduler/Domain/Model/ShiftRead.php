<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\AgentReadContract;
use App\Scheduler\Application\Contract\QueueReadContract;
use App\Scheduler\Application\Contract\ShiftReadContract;
use Symfony\Component\Uid\Uuid;
use DateTimeInterface;

final readonly class ShiftRead implements ShiftReadContract
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
     *     start: string,
     *     end: string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => (string) $this->id,
            'agent' => [
                'id' => (string) $this->agent->getId(),
                'name' => $this->agent->getName(),
            ],
            'queue' => $this->queue->toArray(),
            'start' => $this->start->format('Y-m-d H:i:s'),
            'end' => $this->end->format('Y-m-d H:i:s'),
        ];
    }
}
