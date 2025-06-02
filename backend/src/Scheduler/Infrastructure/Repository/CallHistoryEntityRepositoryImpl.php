<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Repository;

use App\Scheduler\Application\Repository\CallHistoryEntityRepository;
use App\Scheduler\Domain\Entity\CallHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<CallHistory>
 */
class CallHistoryEntityRepositoryImpl extends ServiceEntityRepository implements CallHistoryEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallHistory::class);
    }

    /**
     * @param Uuid $agentId
     * @param list<Uuid> $queueIds
     * @return list<CallHistory>
     */
    public function findByAgentIdAndQueueIds(Uuid $agentId, array $queueIds): array
    {
        $fromDate = new \DateTimeImmutable('-3 months');

        $qb = $this->createQueryBuilder('ch')
            ->where('ch.agent = :agent')
            ->andWhere('ch.date >= :fromDate')
            ->setParameter('agent', $agentId->toBinary(), 'binary')
            ->setParameter('fromDate', $fromDate)
            ->orderBy('ch.date', 'DESC');

        if (!empty($queueIds)) {
            $qb->innerJoin('ch.queue', 'q')
                ->andWhere('q.id IN (:queueIds)')
                ->setParameter('queueIds', array_map(fn(Uuid $id) => $id->toBinary(), $queueIds));
        }

        /** @var list<CallHistory>|null $data */
        $data = $qb->getQuery()->getResult();

        return is_array($data) ? $data : [];
    }
}
