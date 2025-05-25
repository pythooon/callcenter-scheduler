<?php

declare(strict_types=1);

namespace App\Tests\Scheduler\Domain\Model;

use App\Scheduler\Domain\Model\QueueRead;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class QueueReadTest extends TestCase
{
    private QueueRead $queueRead;

    protected function setUp(): void
    {
        $this->queueRead = new QueueRead(Uuid::v4(), 'Queue 1');
    }

    public function testGetId(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->queueRead->getId());
    }

    public function testGetName(): void
    {
        $this->assertSame('Queue 1', $this->queueRead->getName());
    }

    public function testToArray(): void
    {
        $result = $this->queueRead->toArray();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame((string) $this->queueRead->getId(), $result['id']);
        $this->assertSame('Queue 1', $result['name']);
    }
}
