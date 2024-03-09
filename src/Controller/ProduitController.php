<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Dompdf\Options;
use Symfony\Component\Security\Core\Security;
#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }
    #[Route('/front', name: 'app_produit_indexf', methods: ['GET'])]
    public function indexfff(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/indexf.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/produits/pdf', name: 'contrats_pdf')]
    public function generatePdf(): Response
    {
        // Fetch all Contrat entities
        $produit = $this->getDoctrine()->getRepository(Produit::class)->findAll();

        // Create a Dompdf instance
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);

        // Render the Twig template
        $html = $this->renderView('produit/pdf_all.html.twig', [
            'produit' => $produit,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // Set paper size (optional)
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first render the view, then create the PDF)
        $dompdf->render();

        // Stream the file for download
        $output = $dompdf->output();

        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="contrats.pdf"');

        return $response;
    }
    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger,Security $s): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $user = $s->getUser();
        if($user)
        {
            $produit->setUser($user);
        }
        $form->handleRequest($request);
        
       
        if ($form->isSubmitted() && $form->isValid()) {
            
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('image')->getData();
   
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
                    // Move the file to the directory where your images are stored
                    try {
                        $imageFile->move(
                            $this->getParameter('img_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Handle the exception if something happens during the file upload
                    }
    
                    // Update the 'image' property to store the file name instead of its contents
                    $produit->setImage($newFilename);
                }
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a été ajouté avec succès.');

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
    #[Route('ff/{id}', name: 'app_produit_showf', methods: ['GET'])]
    public function showf(Produit $produit): Response
    {
        return $this->render('produit/showf.html.twig', [
            'produit' => $produit,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('img_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
    
                $produit->setImage($newFilename);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a été modifié avec succès.');

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit a été supprimée avec succès.');

        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('dzzdzdzde/{id}', name: 'dislike')]
    public function dislikeBadge(Produit $res): Response
    {
        if ($res->checkAndDeleteIfRequired()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($res);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_indexf');
            $this->addFlash('info', 'Le produit a été bloqué avec succès.');

        } else {
            $res->incrementDislikes();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('info', 'Le produit disliked.');

            return $this->redirectToRoute('app_produit_indexf');
        }
    }
    #[Route('/produit/{id}', name: 'like')]
    public function likeBadge(Produit $res): Response
    {
        $res->incrementLikes();
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('Info', 'Le produit liked.');

        return $this->redirectToRoute('app_produit_indexf');
    }
}
