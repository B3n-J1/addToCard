<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/categories', name: 'admin/categories')]
class CategoriesAdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $repositories): Response
    {
        $categories = $repositories->findAll(); 
        return $this->render('admin/categoriesadmin/index.html.twig', [
            'categories' => $categories,
            
        ]);
        
    }
}
