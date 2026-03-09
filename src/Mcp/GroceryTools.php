<?php

namespace App\Mcp;

use App\Entity\GroceryItem;
use App\Entity\GroceryList;
use App\Repository\GroceryItemRepository;
use App\Repository\GroceryListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mcp\Capability\Attribute\McpTool;

/**
 * MCP Tools for the grocery list demo.
 *
 * These tools are automatically discovered by symfony/mcp-bundle
 * and exposed via stdio (Claude Desktop) or HTTP transport.
 */
class GroceryTools
{
    public function __construct(
        private EntityManagerInterface $em,
        private GroceryListRepository $listRepo,
        private GroceryItemRepository $itemRepo,
    ) {}

    #[McpTool(name: 'get-lists', description: 'Get all grocery lists with their items')]
    public function getLists(): string
    {
        $lists = $this->listRepo->findAllOrdered();

        return json_encode([
            'lists' => array_map(fn(GroceryList $l) => $l->toArray(), $lists),
        ], JSON_THROW_ON_ERROR);
    }

    #[McpTool(name: 'create-list', description: 'Create a new grocery list')]
    public function createList(string $name): string
    {
        $list = new GroceryList();
        $list->setName($name);
        $list->setPosition($this->listRepo->getNextPosition());

        $this->em->persist($list);
        $this->em->flush();

        return json_encode(['success' => true, 'list' => $list->toArray()], JSON_THROW_ON_ERROR);
    }

    #[McpTool(name: 'delete-list', description: 'Delete a grocery list and all its items')]
    public function deleteList(int $listId): string
    {
        $list = $this->listRepo->find($listId);

        if (!$list) {
            return json_encode(['error' => 'List not found']);
        }

        $this->em->remove($list);
        $this->em->flush();

        return json_encode(['success' => true]);
    }

    #[McpTool(name: 'rename-list', description: 'Rename a grocery list')]
    public function renameList(int $listId, string $name): string
    {
        $list = $this->listRepo->find($listId);

        if (!$list) {
            return json_encode(['error' => 'List not found']);
        }

        $list->setName($name);
        $this->em->flush();

        return json_encode(['success' => true]);
    }

    #[McpTool(name: 'add-item', description: 'Add an item to a grocery list')]
    public function addItem(int $listId, string $name): string
    {
        $list = $this->listRepo->find($listId);

        if (!$list) {
            return json_encode(['error' => 'List not found']);
        }

        $item = new GroceryItem();
        $item->setName($name);
        $item->setGroceryList($list);

        $this->em->persist($item);
        $this->em->flush();

        return json_encode(['success' => true, 'item' => $item->toArray()], JSON_THROW_ON_ERROR);
    }

    #[McpTool(name: 'remove-item', description: 'Remove an item from a grocery list')]
    public function removeItem(int $itemId): string
    {
        $item = $this->itemRepo->find($itemId);

        if (!$item) {
            return json_encode(['error' => 'Item not found']);
        }

        $this->em->remove($item);
        $this->em->flush();

        return json_encode(['success' => true]);
    }

    #[McpTool(name: 'toggle-item', description: 'Check or uncheck a grocery item')]
    public function toggleItem(int $itemId): string
    {
        $item = $this->itemRepo->find($itemId);

        if (!$item) {
            return json_encode(['error' => 'Item not found']);
        }

        $item->toggle();
        $this->em->flush();

        return json_encode(['success' => true, 'checked' => $item->isChecked()]);
    }

    #[McpTool(name: 'edit-item', description: 'Rename a grocery item')]
    public function editItem(int $itemId, string $name): string
    {
        $item = $this->itemRepo->find($itemId);

        if (!$item) {
            return json_encode(['error' => 'Item not found']);
        }

        $item->setName($name);
        $this->em->flush();

        return json_encode(['success' => true]);
    }

    #[McpTool(name: 'move-item', description: 'Move an item from one list to another')]
    public function moveItem(int $itemId, int $targetListId): string
    {
        $item = $this->itemRepo->find($itemId);

        if (!$item) {
            return json_encode(['error' => 'Item not found']);
        }

        $targetList = $this->listRepo->find($targetListId);

        if (!$targetList) {
            return json_encode(['error' => 'Target list not found']);
        }

        $item->setGroceryList($targetList);
        $this->em->flush();

        return json_encode(['success' => true]);
    }
}
