<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\QueueListContract;
use App\Scheduler\Application\Repository\QueueEntityRepository;
use App\Scheduler\Application\Repository\QueueRepository;
use App\Scheduler\Domain\Mapper\QueueMapper;

final readonly class QueueRepositoryImpl implements QueueRepository
{
    public function __construct(private QueueEntityRepository $entityRepository, private QueueMapper $mapper)
    {
    }

    public function findAll(): QueueListContract
    {
        return $this->mapper::mapArrayToListContract($this->entityRepository->findAll());
    }
}
