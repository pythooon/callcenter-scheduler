<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Repository;

use App\Scheduler\Application\Repository\AgentEntityRepository;
use App\Scheduler\Application\Repository\EfficiencyEntityRepository;
use App\Scheduler\Application\Repository\QueueEntityRepository;
use App\Scheduler\Domain\Entity\Efficiency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

use function is_array;

/**
 * @extends ServiceEntityRepository<Efficiency>
 */
final class EfficiencyEntityRepositoryImpl extends ServiceEntityRepository implements EfficiencyEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly AgentEntityRepository $agentEntityRepository,
        private readonly QueueEntityRepository $queueEntityRepository
    ) {
        parent::__construct($registry, Efficiency::class);
    }

    public function findByQueueId(Uuid $id): array
    {
        /** @var list<Efficiency>|null $data */
        $data = $this->createQueryBuilder('e')
            ->andWhere('e.queue = :queueId')
            ->setParameter('queueId', $id->toBinary(), 'binary')
            ->getQuery()
            ->getResult();

        return is_array($data) ? $data : [];
    }

    public function upsert(Efficiency $efficiency): void
    {
        $em = $this->getEntityManager();

        $agentId = $efficiency->getAgent()->getId();
        $queueId = $efficiency->getQueue()->getId();

        $existing = $this->createQueryBuilder('e')
            ->join('e.agent', 'a')
            ->join('e.queue', 'q')
            ->where('a.id = :agentId')
            ->andWhere('q.id = :queueId')
            ->setParameter('agentId', $agentId->toBinary(), 'binary')
            ->setParameter('queueId', $queueId->toBinary(), 'binary')
            ->getQuery()
            ->getOneOrNullResult();

        if ($existing instanceof Efficiency) {
            $existing->setScore($efficiency->getScore());
            $em->flush();
            return;
        }

        $agentRef = $this->agentEntityRepository->findOrFail($agentId);
        $queueRef = $this->queueEntityRepository->findOrFail($queueId);

        $efficiency->setAgent($agentRef);
        $efficiency->setQueue($queueRef);

        $em->persist($efficiency);
        $em->flush();
    }
}
