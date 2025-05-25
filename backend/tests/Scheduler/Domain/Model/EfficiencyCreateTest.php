<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\EfficiencyCreate;
use App\Scheduler\Domain\Model\AgentRead;
use App\Scheduler\Domain\Model\QueueList;
use App\Scheduler\Domain\Model\QueueRead;
use Symfony\Component\Uid\Uuid;
use PHPUnit\Framework\TestCase;

class EfficiencyCreateTest extends TestCase
{
    private Uuid $uuid;
    private AgentRead $agent;
    private QueueRead $queue;
    private float $score;
    private EfficiencyCreate $efficiencyCreate;

    protected function setUp(): void
    {
        $this->uuid = Uuid::v4();
        $this->agent = new AgentRead($this->uuid, 'Test Agent', new QueueList());
        $this->queue = new QueueRead(Uuid::v4(), 'Test Queue');
        $this->score = 85.5;

        $this->efficiencyCreate = new EfficiencyCreate($this->uuid, $this->agent, $this->queue, $this->score);
    }

    public function testGetId(): void
    {
        $this->assertSame($this->uuid, $this->efficiencyCreate->getId());
    }

    public function testGetAgent(): void
    {
        $this->assertSame($this->agent, $this->efficiencyCreate->getAgent());
    }

    public function testGetQueue(): void
    {
        $this->assertSame($this->queue, $this->efficiencyCreate->getQueue());
    }

    public function testGetScore(): void
    {
        $this->assertEquals($this->score, $this->efficiencyCreate->getScore());
    }
}
