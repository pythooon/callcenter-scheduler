<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;
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
     *     agent: array{
     *         id: string,
     *         name: string
     *     },
     *     score: float
     * }
     */
    public function toArray(): array;
}
