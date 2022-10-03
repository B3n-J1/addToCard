<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'orders_')]
class OrdersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository, UsersRepository $users,Orders $orders = null, OrdersDetails $orderDetail = null,  EntityManagerInterface $em): Response
    {
        $cart = $session->get("cart", []);
    
        $user = $this->getUser();
        $userId = $users->find($user);
        $total = 0;
        if(!empty($cart)){
            $order = new Orders();
            $ref =uniqid();
            $order->setReference($ref);
            $order->setUsers($userId);   
            $em->persist($order);
            foreach($cart as $id => $quantity){
                $orderDetail = new OrdersDetails();
                $product = $productsRepository->find($id);
                $totalByProduct = $product->getPrice() * $quantity;
                $total += $product->getPrice() * $quantity;
                $orderDetail->setProducts($product);
                $orderDetail->setQuantity($quantity);
                $orderDetail->setPrice($totalByProduct);
                $orderDetail->setOrders($order);
                $em->persist($orderDetail);
                $em->flush();
            }
            $total = 0;
            $session->set("cart", []);
        }else{
            $cartData = [];
            $this->addFlash("alert", "Votre panier est vide");
            return $this->render('cart/index.html.twig',  compact("cartData", "total"));
            
        }
        $cartData = [];
        $this->addFlash("success", "Votre commande à bien été éffectué !");
        return $this->redirectToRoute('app_main');
    }
}
