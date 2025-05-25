<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'predictions')]
class Prediction
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Queue::class)]
    #[ORM\JoinColumn(name: 'queue_id', referencedColumnName: 'id', nullable: false)]
    private Queue $queue;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $time;

    #[ORM\Column(type: 'integer')]
    private int $occupancy;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getQueue(): Queue
    {
        return $this->queue;
    }

    public function setQueue(Queue $queue): void
    {
        $this->queue = $queue;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getTime(): \DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): void
    {
        $this->time = $time;
    }

    public function getOccupancy(): int
    {
        return $this->occupancy;
    }

    public function setOccupancy(int $occupancy): void
    {
        $this->occupancy = $occupancy;
    }
}
