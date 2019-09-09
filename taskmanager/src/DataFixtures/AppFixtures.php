<?php

namespace App\DataFixtures;

use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle('Task ' . $i);
            $task->setCreated(new DateTime('now'));
            $task->setStatus('new');
            $task->setDescription('TODO TASK');
        }
        $manager->flush();
    }
}
