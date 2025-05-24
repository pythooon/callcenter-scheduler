<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Entity;

use App\Scheduler\Infrastructure\Repository\AgentEntityRepositoryImpl;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgentEntityRepositoryImpl::class)]
class Agent
{
    #[ORM\Id, ORM\Column(type: 'uuid'), ORM\GeneratedValue(strategy: 'NONE')]
    private Uuid $id;

    #[ORM\Column(length: 100)]
    private string $name;

    /**
     * @var Collection<int, Queue>
     */
    #[ORM\ManyToMany(targetEntity: Queue::class)]
    private Collection $queues;

    /**
     * @var Collection<int, Shift>
     */
    #[ORM\OneToMany(targetEntity: Shift::class, mappedBy: 'agent')]
    private Collection $shifts;

    /**
     * @var Collection<int, CallHistory>
     */
    #[ORM\OneToMany(targetEntity: CallHistory::class, mappedBy: 'agent', cascade: ['persist', 'remove'])]
    private Collection $callHistories;

    /**
     * @var Collection<int, Efficiency>
     */
    #[ORM\OneToMany(targetEntity: Efficiency::class, mappedBy: 'agent')]
    private Collection $efficiencies;

    public function __construct()
    {
        $this->queues = new ArrayCollection();
        $this->shifts = new ArrayCollection();
        $this->callHistories = new ArrayCollection();
        $this->efficiencies = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, Queue>
     */
    public function getQueues(): Collection
    {
        return $this->queues;
    }

    public function addQueue(Queue $queue): self
    {
        if (!$this->queues->contains($queue)) {
            $this->queues->add($queue);
        }

        return $this;
    }

    /**
     * @return Collection<int, Shift>
     */
    public function getShifts(): Collection
    {
        return $this->shifts;
    }

    public function addShift(Shift $shift): self
    {
        if (!$this->shifts->contains($shift)) {
            $this->shifts->add($shift);
            $shift->setAgent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, CallHistory>
     */
    public function getCallHistories(): Collection
    {
        return $this->callHistories;
    }

    public function addCallHistory(CallHistory $callHistory): self
    {
        if (!$this->callHistories->contains($callHistory)) {
            $this->callHistories->add($callHistory);
            $callHistory->setAgent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Efficiency>
     */
    public function getEfficiencies(): Collection
    {
        return $this->efficiencies;
    }

    public function addEfficiency(Efficiency $efficiency): self
    {
        if (!$this->efficiencies->contains($efficiency)) {
            $this->efficiencies->add($efficiency);
            $efficiency->setAgent($this);
        }

        return $this;
    }
}
