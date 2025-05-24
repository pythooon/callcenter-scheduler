<?php

declare(strict_types=1);

namespace App\Scheduler\Infrastructure\DataFixtures;

use App\Scheduler\Domain\Entity\Agent;
use App\Scheduler\Domain\Entity\CallHistory;
use App\Scheduler\Domain\Entity\Prediction;
use App\Scheduler\Domain\Entity\Queue;
use App\Scheduler\Domain\Entity\Shift;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

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

        $endDate = new \DateTime();
        $startDate = (clone $endDate)->modify('+1 day');
        $oneMonthLater = (clone $startDate)->modify('+1 month');
        $predictionInterval = new \DateInterval('PT1H');
        $predictionPeriod = new \DatePeriod($startDate, $predictionInterval, $oneMonthLater);

        foreach ($predictionPeriod as $date) {
            if (in_array($date->format('N'), [6, 7])) {
                continue;
            }

            foreach ($queues as $queueName) {
                $queue = $manager->getRepository(Queue::class)->findOneBy(['name' => $queueName]);

                if ($queue) {
                    if ($date->format('H') >= 10 && $date->format('H') < 18) {
                        $time = (clone $date)->setTime((int) $date->format('H'), 0);
                    } elseif ($date->format('H') >= 7 && $date->format('H') < 10) {
                        $randomHour = random_int(7, 9);
                        $time = (clone $date)->setTime($randomHour, 0);
                    } elseif ($date->format('H') >= 18 && $date->format('H') < 20) {
                        $randomHour = random_int(18, 19);
                        $time = (clone $date)->setTime($randomHour, 0);
                    } else {
                        continue;
                    }

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
