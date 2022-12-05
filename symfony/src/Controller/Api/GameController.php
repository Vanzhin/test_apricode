<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\Genre;
use App\Repository\GameRepository;
use App\Service\ApiGameResponse;
use App\Service\GameDataLoader;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/api/game', name: 'app_api_game', methods: ['GET'])]
    public function index(Request $request, Pagination $pagination, GameRepository $repository, ApiGameResponse $response): JsonResponse
    {
        $pageNumber = $request->query->getInt('page', 1);
        $games = $pagination->get($repository->findAllQuery(), $pageNumber);

        return $this->json($response->index($games, $pageNumber), 200, [], ['groups' => 'main']);

    }

    #[Route('/api/game/genre/{id<\d+>}', name: 'app_api_game_genre', methods: ['GET'])]
    public function genreGames(Request $request, EntityManagerInterface $em, int $id, ApiGameResponse $response): JsonResponse
    {
        $genre = $em->find(Genre::class, $id);
        $pageNumber = $request->query->getInt('page', 1);

        return $this->json($response->genreGames($genre, $pageNumber), 200, [], ['groups' => 'genre']);

    }

    #[Route('/api/game/{id<\d+>}', name: 'app_api_game_show', methods: ['GET'])]
    public function show(GameRepository $gameRepository, int $id, ApiGameResponse $response): JsonResponse
    {
        return $this->json($response->show($gameRepository->find($id)), 200, [], ['groups' => 'main']);

    }

    #[Route('/api/game/{id<\d+>}', name: 'app_api_game_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, ApiGameResponse $response): JsonResponse
    {
        $game = $em->find(Game::class, $id);

        return $this->json($response->delete($game), 200, [], ['groups' => 'main']);
    }

    #[Route('/api/game', name: 'app_api_game_create', methods: ['POST'])]
    public function create(Request $request, GameDataLoader $loader): JsonResponse
    {
        $data = $request->request->all();
        $game = new Game();

        return $this->json($loader->handle($data, $game), 200, [], ['groups' => 'main']);

    }

    #[Route('/api/game/{id<\d+>}', name: 'app_api_game_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $em, int $id, GameDataLoader $loader): JsonResponse
    {
        $data = $request->request->all();
        $game = $em->find(Game::class, $id);
        if (!$game) {
            return $this->json(['error' => 'no games presented']);
        }
        return $this->json($loader->handle($data, $game), 200, [], ['groups' => 'main']);

    }


}
