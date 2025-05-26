<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;
use Symfony\Component\Uid\Uuid;

interface AgentReadContract extends Arrayable
{
    public function getId(): Uuid;

    public function getName(): string;

    public function getQueues(): QueueListContract;

    public function addEfficiency(EfficiencyReadContract $efficiencyReadContract): void;

    public function getEfficiencyListContract(): EfficiencyListContract;

    public function getScore(Uuid $queueId): float;

    /**
     * @return array{
     *     id: string,
     *     name: string
     * }
     */
    public function toArray(): array;
}
