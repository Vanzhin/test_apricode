<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/api/game', name: 'app_api_game', methods: ['GET'])]
    public function index(GameRepository $gameRepository, PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $games = $gameRepository->findAllQuery();
        $pagination = $paginator->paginate(
            $games, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        if ($pagination->count()) {
            $json = $this->json(['games' => $pagination, 'page' => $request->query->getInt('page', 1)], 200, [], ['groups' => 'main']);
        } else {
            $json = $this->json(['error' => 'no games presented', 'page' => $request->query->getInt('page', 1)]);
        }
        return ($json);

    }

    #[Route('/api/game/{id<\d+>}', name: 'app_api_game_show', methods: ['GET'])]
    public function show(GameRepository $gameRepository, int $id): JsonResponse
    {
        $game = $gameRepository->find($id);
        if ($game) {
            $json = $this->json($game, 200, [], ['groups' => 'main']);
        } else {
            $json = $this->json(['error' => 'no game presented'], 200);
        }
        return ($json);

    }

    #[Route('/api/game/{id<\d+>}', name: 'app_api_game_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $game = $em->find(Game::class, $id);
        if ($game) {
            $json = $this->json(['deleted' => 'ok', 'game' => $game], 200, [], ['groups' => 'main']);
            $em->remove($game);
            $em->flush();
        } else {
            $json = $this->json(['error' => 'no game presented'], 200);
        }
        return ($json);

    }
}
