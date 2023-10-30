<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/categories', name: 'category_')]
class CategoryController extends AbstractController
{

    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category, ProductsRepository $productsRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $product = $productsRepository->findProductsPaginated($page, $category->getSlug(), 8);

        return $this->render('categories/list.html.twig', [
            'controller_name' => 'ProductController',
            'category' => $category,
            'products' => $product
        ]);
    }

}
