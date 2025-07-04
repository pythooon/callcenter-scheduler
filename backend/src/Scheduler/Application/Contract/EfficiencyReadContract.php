<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

interface EfficiencyReadContract extends Arrayable
{
    public function getId(): Uuid;

    public function getAgent(): AgentReadContract;

    public function getQueue(): QueueReadContract;

    public function getScore(): float;

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
    public function toArray(): array;

    public function getStart(): DateTimeInterface;

    public function getEnd(): DateTimeInterface;
}
