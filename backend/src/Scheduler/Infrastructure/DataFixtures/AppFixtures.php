<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\DataFixtures;

use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\CallHistory;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Prediction;
use DateInterval;
use DateMalformedPeriodStringException;
use DatePeriod;
use DateTime;
use Random\RandomException;
use Symfony\Component\Uid\Uuid;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct()
    {
    }

    /**
     * @throws RandomException
     * @throws DateMalformedPeriodStringException
     */
    public function load(ObjectManager $manager): void
    {
        $queues = ['Sales', 'Technical Support', 'Complaints'];

        foreach ($queues as $queueName) {
            $queue = new Queue();
            $queue->setId(Uuid::v4());
            $queue->setName($queueName);
            $manager->persist($queue);
        }
        $manager->flush();

        $agents = [
            ['name' => 'John Doe', 'queues' => ['Sales', 'Complaints', 'Technical Support']],
            ['name' => 'Jane Smith', 'queues' => ['Technical Support', 'Complaints']],
            ['name' => 'Paul Jones', 'queues' => ['Technical Support', 'Complaints']],
            ['name' => 'Ed Harris', 'queues' => ['Technical Support', 'Complaints']],
            ['name' => 'Alice Johnson', 'queues' => ['Sales', 'Technical Support']],
            ['name' => 'Bob Taylor', 'queues' => ['Sales', 'Complaints']],
            ['name' => 'Emily Brown', 'queues' => ['Complaints', 'Sales']],
            ['name' => 'David Green', 'queues' => ['Sales']],
            ['name' => 'Michael Clark', 'queues' => ['Sales', 'Technical Support']],
            ['name' => 'Sara Lee', 'queues' => ['Sales', 'Complaints']],
            ['name' => 'Tom White', 'queues' => ['Technical Support', 'Complaints']],
            ['name' => 'Anna Black', 'queues' => ['Sales', 'Technical Support']],
            ['name' => 'Peter Adams', 'queues' => ['Technical Support', 'Complaints']],
            ['name' => 'Sophia Taylor', 'queues' => ['Sales', 'Complaints']],
            ['name' => 'Lucas Mitchell', 'queues' => ['Technical Support', 'Sales']],
            ['name' => 'Nina Scott', 'queues' => ['Complaints', 'Sales']],
            ['name' => 'Oliver Harris', 'queues' => ['Sales']],
            ['name' => 'Grace King', 'queues' => ['Technical Support', 'Sales']],
        ];

        foreach ($agents as $agentData) {
            $agent = new Agent();
            $agent->setId(Uuid::v4());
            $agent->setName($agentData['name']);

            foreach ($agentData['queues'] as $queueName) {
                $queue = $manager->getRepository(Queue::class)->findOneBy(['name' => $queueName]);
                if ($queue) {
                    $agent->addQueue($queue);
                }
            }

            $manager->persist($agent);
        }
        $manager->flush();

        $startDate = new DateTime('2025-02-01');
        $endDate = new DateTime('2025-05-23');
        $dateInterval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $dateInterval, $endDate);

        foreach ($period as $date) {
            if (in_array($date->format('N'), [6, 7])) {
                continue;
            }

            foreach ($agents as $agentData) {
                $agent = $manager->getRepository(Agent::class)->findOneBy(['name' => $agentData['name']]);
                if (!$agent) {
                    continue;
                }
                $queueName = $agentData['queues'][array_rand($agentData['queues'])];
                $queue = $manager->getRepository(Queue::class)->findOneBy(['name' => $queueName]);
                if (!$queue) {
                    continue;
                }

                if (random_int(0, 100) < 60) {
                    $startHour = random_int(7, 18);
                    $shiftStart = (clone $date)->setTime($startHour, 0, 0);
                    $shiftDuration = random_int(2, 10);

                    for ($i = 0; $i < $shiftDuration; $i++) {
                        $shiftEnd = (clone $shiftStart)->modify('+1 hour');

                        $shiftStart = $shiftEnd;
                    }

                    $midShift = (clone $shiftStart)->modify('+2 hours');
                    $callHistory = new CallHistory();
                    $callHistory->setId(Uuid::v4());
                    $callHistory->setAgent($agent);
                    $callHistory->setQueue($queue);
                    $callHistory->setDate($midShift);
                    $callHistory->setCallsCount(random_int(10, 60));

                    $manager->persist($callHistory);
                }
            }
        }

        $manager->flush();

        $endDate = new DateTime();
        $startDate = (clone $endDate)->modify('+1 day');
        $oneMonthLater = (clone $startDate)->modify('+1 month');
        $predictionInterval = new DateInterval('PT1H');
        $predictionPeriod = new DatePeriod($startDate, $predictionInterval, $oneMonthLater);
        $existingPredictions = [];

        foreach ($predictionPeriod as $date) {
            if (in_array($date->format('N'), [6, 7])) {
                continue;
            }

            foreach ($queues as $queueName) {
                $queue = $manager->getRepository(Queue::class)->findOneBy(['name' => $queueName]);

                if ($queue) {
                    $hour = (int) $date->format('H');
                    if ($hour >= 10 && $hour < 18) {
                        $time = (clone $date)->setTime($hour, 0);
                    } elseif ($hour >= 7 && $hour < 10) {
                        $randomHour = random_int(7, 9);
                        $time = (clone $date)->setTime($randomHour, 0);
                    } elseif ($hour >= 18 && $hour < 20) {
                        $randomHour = random_int(18, 19);
                        $time = (clone $date)->setTime($randomHour, 0);
                    } else {
                        continue;
                    }

                    $key = $queue->getId()->toRfc4122() . '|' . $time->format('Y-m-d H');

                    if (!isset($existingPredictions[$key])) {
                        $existingPredictions[$key] = true;

                        $occupancy = random_int(1, 10) * 10;

                        $prediction = new Prediction();
                        $prediction->setId(Uuid::v4());
                        $prediction->setQueue($queue);
                        $prediction->setDate((clone $time));
                        $prediction->setTime($time);
                        $prediction->setOccupancy($occupancy);

                        $manager->persist($prediction);
                    }
                }
            }
        }

        $manager->flush();
    }
}
