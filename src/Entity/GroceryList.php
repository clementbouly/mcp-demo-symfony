<?php

namespace App\Entity;

use App\Repository\GroceryListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroceryListRepository::class)]
class GroceryList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private string $name;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private int $createdAt;

    /** @var Collection<int, GroceryItem> */
    #[ORM\OneToMany(targetEntity: GroceryItem::class, mappedBy: 'groceryList', cascade: ['remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = time();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = mb_substr(strip_tags($name), 0, 25);
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /** @return Collection<int, GroceryItem> */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(GroceryItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setGroceryList($this);
        }
        return $this;
    }

    public function removeItem(GroceryItem $item): static
    {
        $this->items->removeElement($item);
        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'order' => $this->position,
            'createdAt' => $this->createdAt,
            'items' => array_map(
                fn(GroceryItem $item) => $item->toArray(),
                $this->items->toArray()
            ),
        ];
    }
}
