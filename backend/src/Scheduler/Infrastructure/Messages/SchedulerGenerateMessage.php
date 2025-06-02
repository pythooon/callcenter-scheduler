<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Messages;

use App\Scheduler\Application\Contract\ScheduleCreateContract;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(SchedulerGenerateMessageHandler::class)]
final readonly class SchedulerGenerateMessage
{
    public function __construct(private ScheduleCreateContract $scheduleCreateContract)
    {
    }

    public function getScheduleCreateContract(): ScheduleCreateContract
    {
        return $this->scheduleCreateContract;
    }
}
