<?php

namespace App\Controller;

use App\Entity\GroceryItem;
use App\Entity\GroceryList;
use App\Repository\GroceryItemRepository;
use App\Repository\GroceryListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private GroceryListRepository $listRepo,
        private GroceryItemRepository $itemRepo,
    ) {}

    /* ── State hash (polling) ─────────────────────────── */

    #[Route('/state-hash', methods: ['GET'])]
    public function getStateHash(): JsonResponse
    {
        $lists = $this->listRepo->findAllOrdered();
        $data = array_map(fn(GroceryList $l) => $l->toArray(), $lists);

        return $this->json(['hash' => md5(json_encode($data))]);
    }

    /* ── Lists ─────────────────────────────────────────── */

    #[Route('/lists', methods: ['GET'])]
    public function getLists(): JsonResponse
    {
        $lists = $this->listRepo->findAllOrdered();

        return $this->json([
            'lists' => array_map(fn(GroceryList $l) => $l->toArray(), $lists),
        ]);
    }

    #[Route('/lists', methods: ['POST'])]
    public function createList(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return $this->json(['error' => 'Name is required'], 400);
        }

        $list = new GroceryList();
        $list->setName($name);
        $list->setPosition($this->listRepo->getNextPosition());

        $this->em->persist($list);
        $this->em->flush();

        return $this->json(['success' => true, 'list' => $list->toArray()], 201);
    }

    #[Route('/lists/{id}', methods: ['DELETE'])]
    public function deleteList(int $id): JsonResponse
    {
        $list = $this->listRepo->find($id);

        if (!$list) {
            return $this->json(['error' => 'List not found'], 404);
        }

        $this->em->remove($list);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/lists/{id}', methods: ['PUT'])]
    public function renameList(int $id, Request $request): JsonResponse
    {
        $list = $this->listRepo->find($id);

        if (!$list) {
            return $this->json(['error' => 'List not found'], 404);
        }

        $data = $request->toArray();
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return $this->json(['error' => 'Name is required'], 400);
        }

        $list->setName($name);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    /* ── Items ─────────────────────────────────────────── */

    #[Route('/lists/{listId}/items', methods: ['POST'])]
    public function addItem(int $listId, Request $request): JsonResponse
    {
        $list = $this->listRepo->find($listId);

        if (!$list) {
            return $this->json(['error' => 'List not found'], 404);
        }

        $data = $request->toArray();
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return $this->json(['error' => 'Name is required'], 400);
        }

        $item = new GroceryItem();
        $item->setName($name);
        $item->setGroceryList($list);

        $this->em->persist($item);
        $this->em->flush();

        return $this->json(['success' => true, 'item' => $item->toArray()], 201);
    }

    #[Route('/items/{id}', methods: ['DELETE'])]
    public function removeItem(int $id): JsonResponse
    {
        $item = $this->itemRepo->find($id);

        if (!$item) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        $this->em->remove($item);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/items/{id}/toggle', methods: ['PATCH'])]
    public function toggleItem(int $id): JsonResponse
    {
        $item = $this->itemRepo->find($id);

        if (!$item) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        $item->toggle();
        $this->em->flush();

        return $this->json(['success' => true, 'checked' => $item->isChecked()]);
    }

    #[Route('/items/{id}', methods: ['PUT'])]
    public function editItem(int $id, Request $request): JsonResponse
    {
        $item = $this->itemRepo->find($id);

        if (!$item) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        $data = $request->toArray();
        $name = trim($data['name'] ?? '');

        if ($name === '') {
            return $this->json(['error' => 'Name is required'], 400);
        }

        $item->setName($name);
        $this->em->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/items/{id}/move', methods: ['POST'])]
    public function moveItem(int $id, Request $request): JsonResponse
    {
        $item = $this->itemRepo->find($id);

        if (!$item) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        $data = $request->toArray();
        $targetListId = $data['targetListId'] ?? null;
        $targetList = $this->listRepo->find($targetListId);

        if (!$targetList) {
            return $this->json(['error' => 'Target list not found'], 404);
        }

        $item->setGroceryList($targetList);
        $this->em->flush();

        return $this->json(['success' => true]);
    }
}
