<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\UserType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('admin/users', name: 'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $repositories): Response
    {
        $users = $repositories->findByRoleWrongWay('ADMIN'); 
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            
        ]);
        
    }
    #[Route('/new', name:'create')]
    #[Route('/{id}', name:"edit", methods:['GET','POST'])]
      public function createAndEditAction(Users $user = null, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher) 
      {
        if(!$user){
          $user = new Users();
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
          $modif = $user->getId() !== null;
          if (!$modif) {
            $user->setPassword(
              $userPasswordHasher->hashPassword(
                      $user,
                      $form->get('password')->getData()
                  )
            );
          }
          $manager->persist($user);
          $this->addFlash("success", ($modif) ? "La modification a été effectuée" : "L'ajout a été effectuée");
          $manager->flush();

          return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/users/edit.html.twig', [
          'user' => $user,
          'form' => $form->createView(),
          'modification' => $user->getId() !== null
        ]);
      }
      #[Route('/delete/{id}', 'delete', methods:['GET'])]
      public function delete(EntityManagerInterface $em, Users $users): Response {
        $em->remove($users);
        $em->flush();
        $this->addFlash(
            'success',
            'L utilisateur a été supprimé avec succès !'
        );

        return $this->redirectToRoute('admin_users_index');
    }

}