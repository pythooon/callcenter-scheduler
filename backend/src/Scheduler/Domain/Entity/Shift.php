<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Entity;

use App\Scheduler\Infrastructure\Repository\ShiftEntityRepositoryImpl;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShiftEntityRepositoryImpl::class)]
class Shift
{
    #[ORM\Id, ORM\Column(type: 'uuid'), ORM\GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Agent::class, cascade: ['persist'], inversedBy: 'shifts')]
    private Agent $agent;

    #[ORM\ManyToOne(targetEntity: Queue::class, cascade: ['persist'], inversedBy: 'queues')]
    private Queue $queue;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $start;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $end;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getAgent(): Agent
    {
        return $this->agent;
    }

    public function setAgent(Agent $agent): void
    {
        $this->agent = $agent;
    }

    public function getQueue(): Queue
    {
        return $this->queue;
    }

    public function setQueue(Queue $queue): void
    {
        $this->queue = $queue;
    }

    public function getStart(): \DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): \DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): void
    {
        $this->end = $end;
    }
}
