<?php

namespace App\DataFixtures;

use App\Entity\Developer;
use App\Entity\Game;
use App\Entity\Genre;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends BaseFixtures implements DependentFixtureInterface
{

    function loadData(ObjectManager $manager)
    {
        $this->createMany(Game::class, 500, function (Game $article) use ($manager) {
            $title = $this->faker->words(3, true);
            $article->setTitle($title)
                ->setDeveloper($this->getRandomReferences(Developer::class));

            $genres = [];
            for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++) {
                $genres[] = $this->getRandomReferences(Genre::class);
            }
            foreach ($genres as $genre) {
                $article->addGenre($genre);
            }
        });
    }

    public function getDependencies(): array
    {
        return [
            GenreFixtures::class,
            DeveloperFixtures::class,
        ];
    }
}
