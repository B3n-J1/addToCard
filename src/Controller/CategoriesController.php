<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function list(Categories $category, CategoriesRepository $categoriesRepository): Response
    {
        //On va chercher la liste des categories enfants de la catégorie
        $subcategories = $categoriesRepository->findBy(['parent' => $category->getId()]);
        return $this->render('categories/list.html.twig', compact('category', 'subcategories'));
        // Syntaxe alternative
        // return $this->render('categories/list.html.twig', [
        //     'category' => $category,
        //     'products' => $products
        // ]);
    }
    #[Route('/{parentSlug}/{slug}', name: 'sublist')]
    public function sublist(Categories $category, CategoriesRepository $categoriesRepository): Response
    {
        //On va chercher la liste des produits de la catégorie
        $products = $category->getProducts();
        return $this->render('categories/sublist.html.twig', compact('category', 'products'));
        
    }
}
