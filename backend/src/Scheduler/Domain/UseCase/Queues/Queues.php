<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\Queues;

use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Repository\QueueRepository;

final readonly class Queues
{
    public function __construct(private QueueRepository $repository)
    {
    }

    public function run(): QueueListContract
    {
        return $this->repository->findAll();
    }
}
