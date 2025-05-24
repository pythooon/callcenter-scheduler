<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Entity;

use App\Scheduler\Infrastructure\Repository\EfficiencyEntityRepositoryImpl;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EfficiencyEntityRepositoryImpl::class)]
class Efficiency
{
    #[ORM\Id, ORM\Column(type: 'uuid'), ORM\GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Agent::class, cascade: ['persist'])]
    private Agent $agent;

    #[ORM\ManyToOne(targetEntity: Queue::class, cascade: ['persist'])]
    private Queue $queue;

    #[ORM\Column(type: "float")]
    private float $score;

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getScore(): float
    {
        return $this->score;
    }

    public function setScore(float $score): void
    {
        $this->score = $score;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }
}
