<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository ): Response
    {
        $cart = $session->get("cart", []);

        // On "fabrique" les données
        $cartData = [];
        $total = 0;

        foreach($cart as $id => $quantity){
            $product = $productsRepository->find($id);
            $cartData[] = [
                "product" => $product,
                "quantity" => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }

        return $this->render('cart/index.html.twig', compact("cartData", "total"));
    }

    #[Route('/add/{id}', name: 'add')]
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

        return $this->redirectToRoute("cart_index");
        
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if(!empty($cart[$id])){
            if($cart[$id] > 1){
                $cart[$id]--;
            }else{
                unset($cart[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("cart", $cart);

        return $this->redirectToRoute("cart_index");
    }
        
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products $product, SessionInterface $session)
    {
        // On récupère le panier actuel
        $cart = $session->get("cart", []);
        $id = $product->getId();

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        // On sauvegarde dans la session
        $session->set("cart", $cart);

        return $this->redirectToRoute("cart_index");
    }

    #[Route('/delete', name: 'delete_all')]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("cart");

        return $this->redirectToRoute("cart_index");
    }
}
