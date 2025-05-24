<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Entity;

use App\Scheduler\Infrastructure\Repository\CallHistoryEntityRepositoryImpl;
use Symfony\Component\Uid\Uuid;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CallHistoryEntityRepositoryImpl::class)]
class CallHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(name: 'agent_id', referencedColumnName: 'id', nullable: false)]
    private Agent $agent;

    #[ORM\ManyToOne(targetEntity: Queue::class)]
    #[ORM\JoinColumn(name: 'queue_id', referencedColumnName: 'id', nullable: false)]
    private Queue $queue;

    #[ORM\Column(type: "datetime")]
    private DateTimeInterface $date;

    #[ORM\Column(type: "integer")]
    private int $callsCount;

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

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getCallsCount(): int
    {
        return $this->callsCount;
    }

    public function setCallsCount(int $callsCount): void
    {
        $this->callsCount = $callsCount;
    }
}
