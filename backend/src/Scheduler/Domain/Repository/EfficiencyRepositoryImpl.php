<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Repository;

use App\Scheduler\Application\Contract\EfficiencyCreateContract;
use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Repository\EfficiencyEntityRepository;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Domain\Entity\Efficiency;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;
use App\Scheduler\Domain\Model\EfficiencyList;
use Symfony\Component\Uid\Uuid;

use function array_map;

final class EfficiencyRepositoryImpl implements EfficiencyRepository
{
    public function __construct(
        private readonly EfficiencyEntityRepository $entityRepository,
        private readonly EfficiencyMapper $mapper
    ) {
    }

    public function upsert(EfficiencyCreateContract $contract): void
    {
        $entity = $this->mapper::mapCreateContractToEntity($contract);
        $this->entityRepository->upsert($entity);
    }

    public function findAll(): EfficiencyListContract
    {
        $entities = $this->entityRepository->findAll();

        return $this->prepareEfficiencyList($entities);
    }

    public function findByQueueId(Uuid $id): EfficiencyListContract
    {
        $entities = $this->entityRepository->findByQueueId($id);
        return $this->prepareEfficiencyList($entities);
    }

    /**
     * @param list<Efficiency> $entities
     * @return EfficiencyListContract
     */
    private function prepareEfficiencyList(array $entities): EfficiencyListContract
    {
        $efficiencyList = new EfficiencyList();
        array_map(
            fn($entity) => $efficiencyList->addItem(
                $this->mapper->mapEntityToReadContract($entity)
            ),
            $entities
        );
        return $efficiencyList;
    }
}
