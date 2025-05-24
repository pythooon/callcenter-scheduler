<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\Controller;

use App\Scheduler\Application\SchedulerFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ScheduleController extends AbstractController
{
    public function __construct(private SchedulerFacade $facade)
    {
    }

    #[Route("/api/scheduler/calculate-efficiency", name: "scheduler_calculate_efficiency", methods: ["POST"])]
    public function calculateEfficiency(): JsonResponse
    {
        $list = $this->facade->calculateEfficiency();
        return $this->json($list->toArray());
    }

    #[Route("/api/scheduler/generate", name: "scheduler_generate", methods: ["POST"])]
    public function scheduleGenerate(): JsonResponse
    {
        $this->facade->scheduleGenerate();
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
    public function shifts(): JsonResponse
    {
        $shifts = $this->facade->shifts();
        return $this->json($shifts->toArray());
    }
}
