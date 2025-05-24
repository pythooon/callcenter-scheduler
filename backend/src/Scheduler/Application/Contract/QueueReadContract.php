<?php

declare(strict_types=1);

namespace App\Scheduler\Application\Contract;

use App\Common\Contract\Arrayable;
use Symfony\Component\Uid\Uuid;

interface QueueReadContract extends Arrayable
{
    public function getId(): Uuid;

    public function getName(): string;

    /**
     * @return array{
     *     id: string,
     *     name: string
     * }
     */
    public function toArray(): array;
}
