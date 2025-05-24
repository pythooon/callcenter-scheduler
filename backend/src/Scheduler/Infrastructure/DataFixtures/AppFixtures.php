<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\DataFixtures;

use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\CallHistory;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Shift;
use App\Scheduler\Domain\Entity\Prediction;
use Symfony\Component\Uid\Uuid;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct()
    {
    }

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

        $startDate = new \DateTime('2025-04-01');
        $endDate = new \DateTime('2025-06-01');
        $dateInterval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $dateInterval, $endDate);

        foreach ($period as $date) {
            if (in_array($date->format('N'), [6, 7])) {
                continue;
            }

            foreach ($agents as $agentData) {
                $agent = $manager->getRepository(Agent::class)->findOneBy(['name' => $agentData['name']]);
                if (!$agent) {
                    echo 'Agent not found: ' . $agentData['name'] . PHP_EOL;
                    continue;
                }
                $queueName = $agentData['queues'][array_rand($agentData['queues'])];
                $queue = $manager->getRepository(Queue::class)->findOneBy(['name' => $queueName]);
                if (!$queue) {
                    echo 'Queue not found: ' . $queueName . PHP_EOL;
                    continue;
                }

                if (random_int(0, 100) < 80) {
                    $startHour = random_int(7, 18);
                    $shiftStart = (clone $date)->setTime($startHour, 0, 0);
                    $shiftDuration = random_int(4, 12);

                    for ($i = 0; $i < $shiftDuration; $i++) {
                        $shiftEnd = (clone $shiftStart)->modify('+1 hour');

                        $shift = new Shift();
                        $shift->setId(Uuid::v4());
                        $shift->setAgent($agent);
                        $shift->setStart($shiftStart);
                        $shift->setEnd($shiftEnd);
                        $shift->setQueue($queue);

                        $manager->persist($shift);
                        $shiftStart = $shiftEnd;
                    }

                    $midShift = (clone $shiftStart)->modify('+2 hours');
                    $callHistory = new CallHistory();
                    $callHistory->setId(Uuid::v4());
                    $callHistory->setAgent($agent);
                    $callHistory->setQueue($queue);
                    $callHistory->setDate($midShift);
                    $callHistory->setCallsCount(random_int(20, 80));

                    $manager->persist($callHistory);
                }
            }
        }

        $manager->flush();

        $threeMonthsLater = new \DateTime('2025-07-01');
        $predictionInterval = new \DateInterval('P1D');
        $predictionPeriod = new \DatePeriod($startDate, $predictionInterval, $threeMonthsLater);

        foreach ($predictionPeriod as $date) {
            if (in_array($date->format('N'), [6, 7])) {
                continue;
            }

            foreach ($queues as $queueName) {
                $queue = $manager->getRepository(Queue::class)->findOneBy(['name' => $queueName]);

                if ($queue) {
                    $time = (clone $date)->setTime(random_int(7, 18), 0);
                    $occupancy = random_int(5, 20);

                    $prediction = new Prediction(
                        id: Uuid::v4(),
                        queue: $queue,
                        date: $date,
                        time: $time,
                        occupancy: $occupancy,
                    );

                    $manager->persist($prediction);
                }
            }
        }

        $manager->flush();
    }
}
