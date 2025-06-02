<?php

declare(strict_types=1);

namespace App\Scheduler\Domain\UseCase\CalculateEfficiency;

use App\Scheduler\Application\Contract\EfficiencyListContract;
use App\Scheduler\Application\Repository\AgentRepository;
use App\Scheduler\Application\Repository\CallHistoryRepository;
use App\Scheduler\Application\Repository\EfficiencyRepository;
use App\Scheduler\Domain\Calculator\EfficiencyCalculator;
use App\Scheduler\Domain\Mapper\EfficiencyMapper;
use DateTime;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Throwable;

use function var_dump;

final readonly class CalculateEfficiency
{
    public function __construct(
        private AgentRepository $agentRepository,
        private CallHistoryRepository $callHistoryRepository,
        private EfficiencyRepository $efficiencyRepository,
        private EfficiencyMapper $efficiencyMapper,
        private EfficiencyCalculator $efficiencyCalculator,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param list<Uuid|null> $queueIds
     * @return EfficiencyListContract
     * @throws Throwable
     */
    public function run(
        array $queueIds = []
    ): EfficiencyListContract {
        $start = new DateTime('first day of previous month 00:00')->setTime(0, 0, 0);
        $end = new DateTime('now')->setTime(23, 59, 59);

        try {
            if (empty($queueIds)) {
                $agents = $this->agentRepository->findAll();
            } else {
                /** @var list<Uuid> $queueIds */
                $agents = $this->agentRepository->findByQueueIds($queueIds);
            }
        } catch (Throwable $e) {
            $this->logger->error('Failed to fetch agents', [
                'queueIds' => $queueIds,
                'exception' => $e,
            ]);
            throw $e;
        }

        foreach ($agents->getItems() as $agentReadContract) {
            try {
                $callHistoryList = $this->callHistoryRepository->findByAgentAndQueues($agentReadContract, $queueIds);
                $efficiencies = $this->efficiencyCalculator->calculate(
                    $agentReadContract,
                    $callHistoryList,
                    $start,
                    $end
                );

                foreach ($efficiencies->getItems() as $efficiency) {
                    $this->efficiencyRepository->upsert(
                        $this->efficiencyMapper->mapReadContractToCreateContract($efficiency)
                    );
                }
            } catch (Throwable $e) {
                $this->logger->error('Failed to calculate efficiency for agent', [
                    'agentId' => (string)$agentReadContract->getId(),
                    'agentName' => $agentReadContract->getName(),
                    'exception' => $e,
                ]);
                continue;
            }
        }

        try {
            return $this->efficiencyRepository->findAll();
        } catch (Throwable $e) {
            $this->logger->error('Failed to fetch calculated efficiencies', [
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
