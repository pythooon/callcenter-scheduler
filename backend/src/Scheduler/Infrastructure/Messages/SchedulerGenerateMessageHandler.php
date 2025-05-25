<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Messages;

use App\Scheduler\Application\SchedulerFacade;

class SchedulerGenerateMessageHandler
{
    private SchedulerFacade $facade;

    public function __construct(SchedulerFacade $facade)
    {
        $this->facade = $facade;
    }

    public function __invoke(SchedulerGenerateMessage $message): void
    {
        $this->facade->scheduleGenerate();
    }
}
