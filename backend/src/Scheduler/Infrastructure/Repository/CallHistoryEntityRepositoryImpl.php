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
     * @return list<CallHistory>
     */
    public function findByAgentId(Uuid $agentId): array
    {
        $fromDate = new \DateTimeImmutable('-3 months');

        /** @var list<CallHistory>|null $data */
        $data = $this->createQueryBuilder('ch')
            ->where('ch.agent = :agent')
            ->andWhere('ch.date >= :fromDate')
            ->setParameter('agent', $agentId->toBinary(), 'binary') // ustaw typ jako binary!
            ->setParameter('fromDate', $fromDate)
            ->orderBy('ch.date', 'DESC')
            ->getQuery()
            ->getResult();

        return is_array($data) ? $data : [];
    }
}
