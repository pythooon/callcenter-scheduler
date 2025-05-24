<?php

declare(strict_types=1);

namespace App\Common\Contract;

interface Arrayable
{
    /**
     * @return array<string|int, mixed>
     */
    public function toArray(): array;
}
