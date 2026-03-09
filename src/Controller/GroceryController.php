<?php

namespace App\Controller;

use App\Repository\GroceryListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GroceryController extends AbstractController
{
    #[Route('/', name: 'grocery_index')]
    public function index(GroceryListRepository $listRepo): Response
    {
        $lists = $listRepo->findAllOrdered();

        return $this->render('grocery/index.html.twig', [
            'lists' => $lists,
        ]);
    }
}
