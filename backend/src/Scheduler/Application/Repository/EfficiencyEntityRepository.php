<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\Efficiency;
use Symfony\Component\Uid\Uuid;

interface EfficiencyEntityRepository
{
    public function upsert(Efficiency $efficiency): void;

    /**
     * @return list<Efficiency>
     */
    public function findAll(): array;

    /**
     * @return list<Efficiency>
     */
    public function findByQueueId(Uuid $id): array;
}
