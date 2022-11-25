<?php

namespace App\DataFixtures;

use App\Entity\Developer;
use Doctrine\Persistence\ObjectManager;

class DeveloperFixtures  extends BaseFixtures
{
    function loadData(ObjectManager $manager)
    {
        $this->createMany(Developer::class, 100, function (Developer $developer) use ($manager) {
            $developer
                ->setTitle($this->faker->company());
        });
    }
}
