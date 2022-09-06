<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/categories', name: 'admin_categories_')]
class CategoriesAdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $repositories): Response
    {
        $categories = $repositories->findAll(); 
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
            
        ]);
        
    }
    #[Route('/new', name:'create')]
    #[Route('/{slug}', name:"edit", methods:['GET','POST'])]
      public function createAndEditAction(Categories $category = null, Request $request, EntityManagerInterface $manager) 
      {
        if(!$category){
          $category = new Categories();
        }
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
          $modif = $category->getId() !== null;
          
          $manager->persist($category);
          $this->addFlash("success", ($modif) ? "La modification a été effectuée" : "L'ajout a été effectuée");
          $manager->flush();

          return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/edit.html.twig', [
          'category' => $category,
          'form' => $form->createView(),
          'modification' => $category->getId() !== null
        ]);
      }
      #[Route('/delete/{slug}', 'delete', methods:['GET'])]
      public function delete(EntityManagerInterface $em, Categories $category): Response {
        $em->remove($category);
        $em->flush();
        $this->addFlash(
            'success',
            'La catégorie a été supprimée avec succès !'
        );

        return $this->redirectToRoute('admin_categories_index');
    }
}
