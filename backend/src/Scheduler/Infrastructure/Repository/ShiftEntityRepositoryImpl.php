<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Repository;

use App\Scheduler\Application\Repository\AgentEntityRepository;
use App\Scheduler\Application\Repository\QueueEntityRepository;
use App\Scheduler\Application\Repository\ShiftEntityRepository;
use App\Scheduler\Domain\Entity\Shift;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

use function is_array;

/**
 * @extends ServiceEntityRepository<Shift>
 */
class ShiftEntityRepositoryImpl extends ServiceEntityRepository implements ShiftEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private AgentEntityRepository $agentEntityRepository,
        private QueueEntityRepository $queueEntityRepository
    ) {
        parent::__construct($registry, Shift::class);
    }

    public function upsert(Shift $shift): void
    {
        $em = $this->getEntityManager();

        $agentId = $shift->getAgent()->getId();
        $queueId = $shift->getQueue()->getId();

        $existing = $this->createQueryBuilder('s')
            ->join('s.agent', 'a')
            ->join('s.queue', 'q')
            ->where('a.id = :agentId')
            ->andWhere('q.id = :queueId')
            ->andWhere('s.start = :start')
            ->setParameter('agentId', $agentId, 'uuid')
            ->setParameter('queueId', $queueId, 'uuid')
            ->setParameter('start', $shift->getStart())
            ->getQuery()
            ->getOneOrNullResult();

        if ($existing instanceof Shift) {
            $existing->setEnd($shift->getEnd());
            $em->flush();
            return;
        }

        $agentRef = $this->agentEntityRepository->findOrFail($agentId);
        $queueRef = $this->queueEntityRepository->findOrFail($queueId);

        $shift->setAgent($agentRef);
        $shift->setQueue($queueRef);

        $em->persist($shift);
        $em->flush();
    }

    public function findShiftsBetweenDates(?DateTimeInterface $start, ?DateTimeInterface $end): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if ($start && $end) {
            $startDate = new DateTimeImmutable($start->format('Y-m-d H:i:s'));
            $endDate = new DateTimeImmutable($end->format('Y-m-d') . ' 23:59:59');

            $queryBuilder
                ->where('s.start BETWEEN :start AND :end')
                ->orWhere('s.end BETWEEN :start AND :end')
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate);
        }

        $result = $queryBuilder->getQuery()->getResult();

        $data = is_array($result) ? $result : throw new InvalidArgumentException('Invalid result');
        /** @var list<Shift> $data */
        return $data;
    }
}
