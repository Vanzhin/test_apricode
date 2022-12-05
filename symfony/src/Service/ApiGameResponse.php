<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ApiGameResponse
{


    private EntityManagerInterface $em;
    private Pagination $pagination;

    public function __construct(EntityManagerInterface $em, Pagination $pagination)
    {
        $this->em = $em;
        $this->pagination = $pagination;
    }

    public function index(PaginationInterface $games, int $pageNumber): array
    {
        if ($games->count()) {
            return ['games' => $games, 'page' => $pageNumber];
        } else {
            return ['error' => 'no games presented', 'page' => $pageNumber];
        }
    }

    public function genreGames(?Genre $genre, int $pageNumber): array
    {
        if ($genre) {
            $games = $this->pagination->get($this->em->getRepository(Game::class)->findAllByGenreQuery($genre), $pageNumber);
            if ($games->count()) {
                return ['genre' => $genre, 'games' => $games, 'page' => $pageNumber];
            } else {
                return ['error' => 'no games presented', 'page' => $pageNumber];
            }
        }
        return ['error' => 'no games with this genre presented'];

    }

    public function show(?Game $game): array|Game
    {
        if ($game) {
            return $game;
        } else {
            return ['error' => 'no game presented'];
        }
    }

    public function delete(?Game $game): array
    {
        if ($game) {
            $response = ['deleted' => 'ok'];
            $this->em->remove($game);
            $this->em->flush();
        } else {
            $response = ['error' => 'no game presented'];
        }
        return ($response);
    }
}