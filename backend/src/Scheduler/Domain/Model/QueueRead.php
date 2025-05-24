<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\Model;

use App\Scheduler\Application\Contract\QueueReadContract;
use Symfony\Component\Uid\Uuid;

final readonly class QueueRead implements QueueReadContract
{
    public function __construct(private Uuid $id, private string $name)
    {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     id: string,
     *     name: string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
        ];
    }
}
