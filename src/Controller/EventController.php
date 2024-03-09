<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participation;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use UltraMsg\WhatsAppApi;
use Symfony\Component\Security\Core\Security;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }
    #[Route('/front', name: 'app_event_indexf', methods: ['GET'])]
    public function indexf(EventRepository $eventRepository): Response
    {
        return $this->render('event/indexf.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
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
  
                  $event->setImage($newFilename);
              }
            $entityManager->persist($event);
            $entityManager->flush();
            $nom = $event->getNom();
            $description = $event->getDescription(); 
            $date = $event->getDate()->format('Y-m-d'); 
        
            $this->envoyerMessageWhatsApp($nom, $description, $date);
                    $this->addFlash('success', 'Evenement est Ajouter.');

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

  

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);

    }
    #[Route('ffdf/{id}', name: 'app_event_showf', methods: ['GET'])]
    public function showd(Event $event,EntityManagerInterface $entityManager): Response
    {
        $currentViews = $event->getNbVue();


        
        ///hnaaaaaaaaaaaa/////////////////////
        $nb=$currentViews+1;
        $event->setNbVue($nb);


        $entityManager->persist($event);
        $entityManager->flush();
        return $this->render('event/showf.html.twig', [
            'event' => $event,
        ]);
      
    }

    #[Route('/what', name: 'whatsapp')]
    public function envoyerMessageWhatsApp($nom, $description, $date): Response
    {
        require_once __DIR__ . '/../../vendor/autoload.php'; 
        $ultramsg_token = "4kv1xiah0sjkb4h4"; 
        $instance_id = "instance80452"; 
    
        $client = new WhatsAppApi($ultramsg_token, $instance_id);
    
        $to = "+21653256173"; 
        $body = "Bonjour Monsieur Idriss,\n\nNous vous informons qu'un nouveau Event a été Ajouter dans notre système. Voici les détails :\n\Nom : $nom\Description : $description\nDate d'ajouteé : $date\n\nVeuillez prendre les mesures nécessaires pour examen et suivi.\n\nCordialement.";
    
        $api = $client->sendChatMessage($to, $body);
    
        $image = "https://upload.wikimedia.org/wikipedia/commons/thumb/3/38/Info_Simple.svg/512px-Info_Simple.svg.png";
        $caption = "Image Caption";
        $priority = 10;
        $referenceId = "SDK";
        $nocache = false;
        $imageApi = $client->sendImageMessage($to, $image, $caption, $priority, $referenceId, $nocache);
    
        print_r($api); 
        print_r($imageApi); 
    
        return new Response('WhatsApp messages sent successfully!');
    }
    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EventType::class, $event);
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
    
                $event->setImage($newFilename);
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'Le Event a été modifié avec succès.');

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }
        $this->addFlash('success', 'L\'Event a été supprimer avec succès.');

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('bcc/{id}', name: 'app_event_deletef', methods: ['POST'])]
    public function deleteb(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }
        $this->addFlash('success', 'L\'Event a été supprimer avec succès.');

        return $this->redirectToRoute('app_event_indexf', [], Response::HTTP_SEE_OTHER);
    }
}
