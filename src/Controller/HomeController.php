<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ImagesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ImagesRepository $imagesRepository, CategoriesRepository $categoryRepo): Response
    {
        $images = $imagesRepository->findAll();
        $category = $categoryRepo->findBy([], ['categoryOrder' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'images' => $images,
            'category' => $category
        ]);
    }
}
