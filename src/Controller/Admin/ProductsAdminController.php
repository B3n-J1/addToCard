<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/products', name: 'admin_products_')]
class ProductsAdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $repositories): Response
    {
        $products = $repositories->findAll(); 
        return $this->render('admin/products/index.html.twig', [
            'products' => $products,
            
        ]);
        
    }
    #[Route('/new', name:'create')]
    #[Route('/{slug}', name:"edit", methods:['GET','POST'])]
      public function createAndEditAction(Products $product = null, Request $request, EntityManagerInterface $manager) 
      {
        if(!$product){
          $product = new Products();
        }
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
          $modif = $product->getId() !== null;
          
          $manager->persist($product);
          $this->addFlash("success", ($modif) ? "La modification a été effectuée" : "L'ajout a été effectuée");
          $manager->flush();

          return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
          'product' => $product,
          'form' => $form->createView(),
          'modification' => $product->getId() !== null
        ]);
      }
      #[Route('/delete/{slug}', 'delete', methods:['GET'])]
      public function delete(EntityManagerInterface $em, Products $product): Response {
        $em->remove($product);
        $em->flush();
        $this->addFlash(
            'success',
            'Le produit a été supprimée avec succès !'
        );

        return $this->redirectToRoute('admin_products_index');
    }
}
