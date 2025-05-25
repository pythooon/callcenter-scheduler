<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Messages;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(SchedulerGenerateMessageHandler::class)]
final readonly class SchedulerGenerateMessage
{
}
