<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Repository;

use App\Scheduler\Domain\Entity\Queue;
use Symfony\Component\Uid\Uuid;

interface QueueEntityRepository
{
    /**
     * @return list<Queue>
     */
    public function findAll(): array;

    public function findOrFail(Uuid $id): Queue;
}
