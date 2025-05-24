<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Repository;

use App\Scheduler\Application\Repository\PredictionEntityRepository;
use App\Scheduler\Domain\Entity\Prediction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

use function is_array;

/**
 * @extends ServiceEntityRepository<Prediction>
 */
class PredictionEntityRepositoryImpl extends ServiceEntityRepository implements PredictionEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prediction::class);
    }

    /**
     * @return list<Prediction>
     */
    public function findPredictionByDateAndTime(
        Uuid $queueId,
        \DateTimeInterface $date,
        \DateTimeInterface $time
    ): array {
        /** @var list<Prediction>|null $data */
        $data = $this->createQueryBuilder('p')
            ->where('p.queue = :queue')
            ->andWhere('p.date = :date')
            ->andWhere('p.time = :time')
            ->setParameter('queue', $queueId)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('time', $time->format('H:i:s'))
            ->getQuery()
            ->getOneOrNullResult();

        return is_array($data) ? $data : [];
    }
}
