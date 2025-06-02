<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Controller;

use App\Scheduler\Application\SchedulerFacade;
use App\Scheduler\Domain\Model\ScheduleCreate;
use App\Scheduler\Domain\Request\ScheduleGenerateRequest;
use App\Scheduler\Domain\Request\ShiftsRequest;
use App\Scheduler\Infrastructure\Messages\SchedulerGenerateMessage;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ScheduleController extends AbstractController
{
    public function __construct(private SchedulerFacade $facade, private MessageBusInterface $bus)
    {
    }

    #[Route("/api/scheduler/generate", name: "scheduler_generate", methods: ["POST"])]
    public function scheduleGenerate(ScheduleGenerateRequest $request): JsonResponse
    {
        $scheduleCreate = new ScheduleCreate($request->getQueueId(), $request->getStartDate(), $request->getEndDate());
        $this->bus->dispatch(new SchedulerGenerateMessage($scheduleCreate));
        return $this->json([], 204);
    }

    #[Route("/api/scheduler/agents", name: "scheduler_agents", methods: ["GET"])]
    public function agents(): JsonResponse
    {
        $agents = $this->facade->agents();
        return $this->json($agents->toArray());
    }

    #[Route("/api/scheduler/efficiencies", name: "scheduler_efficiencies", methods: ["GET"])]
    public function efficiencies(): JsonResponse
    {
        $efficiencies = $this->facade->efficiencies();
        return $this->json($efficiencies->toArray());
    }

    #[Route("/api/scheduler/queues", name: "scheduler_queues", methods: ["GET"])]
    public function queues(): JsonResponse
    {
        $queues = $this->facade->queues();
        return $this->json($queues->toArray());
    }

    #[Route("/api/scheduler/predictions", name: "scheduler_predictions", methods: ["GET"])]
    public function predictions(): JsonResponse
    {
        $predictions = $this->facade->predictions();
        return $this->json($predictions->toArray());
    }

    #[Route("/api/scheduler/shifts", name: "scheduler_shifts", methods: ["GET"])]
    public function shifts(Request $request): JsonResponse
    {
        [$startDate, $endDate] = $this->prepareStartAndEndDates($request);
        $shifts = $this->facade->shifts($startDate, $endDate);

        return $this->json($shifts->toArray());
    }

    /**
     * @param Request $request
     * @return list<DateTime|null>
     * @throws \DateMalformedStringException
     */
    private function prepareStartAndEndDates(Request $request): array
    {
        $start = $request->query->get('start_date');
        $end = $request->query->get('end_date');

        $startDate = null;
        $endDate = null;

        if (is_string($start) && strtotime($start) !== false) {
            $startDate = new DateTime($start);
        }

        if (is_string($end) && strtotime($end) !== false) {
            $endDate = new DateTime($end);
        }
        return [$startDate, $endDate];
    }
}
