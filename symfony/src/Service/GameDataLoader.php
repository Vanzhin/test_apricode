<?php

namespace App\Service;

use App\Entity\Developer;
use App\Entity\Game;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameDataLoader
{


    private EntityManagerInterface $em;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function handle(array $data, Game $game): array|Game
    {
        $game = $this->load($data, $game);
        if ($game instanceof Game) {
            return $game;
        }
        return ['errors' => $game];
    }

    private function load(array $data, Game $game): string|Game
    {
        $props['title'] = $data['title'] ?? null;
        $props['developer'] = isset($data['developer']) ? str_replace(' ', '', $data['developer']) : null;
        $props['genres'] = isset($data['genres']) ? explode(',', str_replace(' ', '', $data['genres'])) : [];

        if (!is_null($props['title'])) {
            $game->setTitle($props['title']);
        }

        if (!is_null($props['developer'])) {
            $game->setDeveloper($this->em->find(Developer::class, $data['developer']));
        }

        if (count($props['genres']) > 0) {
            $game->getGenres()->clear();
            foreach ($props['genres'] as $id) {
                if ($this->em->find(Genre::class, $id))
                    $game->addGenre($this->em->find(Genre::class, $id));
            };
        }

        $errors = $this->validator->validate($game);
        if (count($errors) > 0) {
            return (string)$errors;
        }

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }
}