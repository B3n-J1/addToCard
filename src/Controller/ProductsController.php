<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/products', name: 'products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('products/index.html.twig');
    }
    
    #[Route('/{slug}', name: 'details')]
    public function details(Products $product, SessionInterface $session): Response
    {
        $cart = $session->get("cart", []);
        
        return $this->render('products/details.html.twig', compact('product') );
    }

    #[Route('/{slug}/add', name: 'add')]
    public function add(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("cart", $cart);
        $this->addFlash('success', 'Ce produit à bien été ajouté à votre panier !');
        return $this->redirectToRoute("products_details", ['slug'=> $product->getSlug()]);
        
    }
}
