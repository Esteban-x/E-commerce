<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'category_')]
class CategoryController extends AbstractController
{

    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category): Response
    {
        $product = $category->getProducts();

        return $this->render('categories/list.html.twig', [
            'controller_name' => 'ProductController',
            'category' => $category,
            'product' => $product
        ]);
    }

}
