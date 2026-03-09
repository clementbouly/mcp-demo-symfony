<?php

namespace App\Command;

use App\Entity\GroceryItem;
use App\Entity\GroceryList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-grocery',
    description: 'Seed the database with demo grocery lists',
)]
class SeedGroceryCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Clear existing data
        $this->em->createQuery('DELETE FROM App\Entity\GroceryItem')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\GroceryList')->execute();

        // Carrefour
        $carrefour = new GroceryList();
        $carrefour->setName('Carrefour');
        $carrefour->setPosition(0);
        $this->em->persist($carrefour);

        $items = [
            ['name' => 'Lait', 'checked' => true],
            ['name' => 'Oeufs', 'checked' => false],
            ['name' => 'Pain de mie', 'checked' => false],
            ['name' => 'Beurre', 'checked' => true],
        ];

        foreach ($items as $data) {
            $item = new GroceryItem();
            $item->setName($data['name']);
            $item->setChecked($data['checked']);
            $item->setGroceryList($carrefour);
            $this->em->persist($item);
        }

        // Marché
        $marche = new GroceryList();
        $marche->setName('Marché');
        $marche->setPosition(1);
        $this->em->persist($marche);

        $items = [
            ['name' => 'Tomates', 'checked' => false],
            ['name' => 'Fromage de chèvre', 'checked' => false],
            ['name' => 'Basilic', 'checked' => true],
        ];

        foreach ($items as $data) {
            $item = new GroceryItem();
            $item->setName($data['name']);
            $item->setChecked($data['checked']);
            $item->setGroceryList($marche);
            $this->em->persist($item);
        }

        $this->em->flush();

        $io->success('Demo grocery lists seeded successfully!');

        return Command::SUCCESS;
    }
}
