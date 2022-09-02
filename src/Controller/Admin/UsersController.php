<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/users', name: 'admin/users')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $repositories): Response
    {
        $users = $repositories->findAll(); 
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            
        ]);
        
    }
}