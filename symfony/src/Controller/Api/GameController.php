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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    #[Route('/api/game', name: 'app_api_game_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->request->all();
        $game = new Game();
        return $this->entityHandle($data, $validator, $em, $game);

    }

    #[Route('/api/game/{id<\d+>}', name: 'app_api_game_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, int $id): JsonResponse
    {
        $data = $request->request->all();
        $game = $em->find(Game::class, $id);
        if ($game) {
            return $this->entityHandle($data, $validator, $em, $game);

        } else {
            return $this->json(['errors' => 'no game to update'], 200);
        }
    }

    private function entityHandle(array $data, ValidatorInterface $validator, EntityManagerInterface $em, Game $game): JsonResponse
    {
        $game = $game->game($data, $validator, $em, $game);
        if ($game instanceof Game) {
            $em->persist($game);
            $em->flush();
            $json = $this->json($game, 200, [], ['groups' => 'main']);

        } else {
            $json = $this->json(['errors' => $game], 200);
        }
        return ($json);

    }
}
