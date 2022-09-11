<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
      public function createAndEditAction(Products $product = null,Images $images = null, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger) 
      {
        if(!$product){
          $product = new Products();
        }
        
        $images = $product->getImages();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
          $uploaded = $form->get('images')->getData();
            if ($uploaded) {
              foreach($uploaded as $image) {
                # code...
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
    
                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('assets_uploads'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
    
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $im=new Images();
                $im->setName($newFilename);
                $im->setProducts($product);
                $product->addImage($im);
                // $manager->persist($im);
              }
          }
          $modif = $product->getId() !== null;
          $manager->persist($product);
          $this->addFlash("success", ($modif) ? "La modification a été effectuée" : "L'ajout a été effectuée");
          $manager->flush();

          return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
          'product' => $product,
          'images' => $images,
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
      #[Route('/delete/{id}', 'deleteImage', methods:['DELETE'])]
      public function deleteImage(Images $image,Request $request, EntityManagerInterface $em) {
        $data = json_decode($request->getContent(), true);
        
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
          $nom = $image->getName();
          unlink($this->getParameter('assets_uploads').'/'.$nom);
          
          $em->remove($image);
          $em->flush();

          return new JsonResponse(['success' => 1]);
        }
        return new JsonResponse(['error' => 'Token Invalide'], 400);
    }
}
