<?php

namespace App\Entity;

use App\Repository\GroceryItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroceryItemRepository::class)]
class GroceryItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $name;

    #[ORM\Column]
    private bool $checked = false;

    #[ORM\Column]
    private int $createdAt;

    #[ORM\ManyToOne(targetEntity: GroceryList::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private GroceryList $groceryList;

    public function __construct()
    {
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
        $this->name = mb_substr(strip_tags($name), 0, 50);
        return $this;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): static
    {
        $this->checked = $checked;
        return $this;
    }

    public function toggle(): static
    {
        $this->checked = !$this->checked;
        return $this;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getGroceryList(): GroceryList
    {
        return $this->groceryList;
    }

    public function setGroceryList(GroceryList $groceryList): static
    {
        $this->groceryList = $groceryList;
        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'checked' => $this->checked,
            'createdAt' => $this->createdAt,
        ];
    }
}
