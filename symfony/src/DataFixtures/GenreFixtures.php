<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Persistence\ObjectManager;

class GenreFixtures  extends BaseFixtures
{
    function loadData(ObjectManager $manager)
    {
        $this->createMany(Genre::class, 20, function (Genre $genre) use ($manager) {

            $genre
                ->setTitle($this->faker->words($this->faker->numberBetween(1,5), true));
        });
    }

}
