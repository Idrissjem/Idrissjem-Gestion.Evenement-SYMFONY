<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Form\ParticipationType1;
use Symfony\Component\Security\Core\Security;

use App\Repository\ParticipationRepository;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participation')]
class ParticipationController extends AbstractController
{
    #[Route('/', name: 'app_participation_index', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository): Response
    {
        return $this->render('participation/index.html.twig', [
            'participations' => $participationRepository->findAll(),
        ]);
    }
    #[Route('/back', name: 'app_participation_indexb', methods: ['GET'])]
    public function indexbb(ParticipationRepository $participationRepository): Response
    {
        return $this->render('participation/indexb.html.twig', [
            'participations' => $participationRepository->findAll(),
        ]);
    }
    #[Route('/generateExcel', name: 'excel')]
public function generateUserExcel(ParticipationRepository $partRepository): BinaryFileResponse
{
    $participations = $partRepository->findAll();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Define column names
    $columnNames = ['Event', 'Date', 'Prenom', 'Nom', 'Tel'];

    // Set the entire first row at once and make it bold
    $sheet->fromArray([$columnNames], null, 'A1');
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);

    $sn = 2; // Start from the second row
    foreach ($participations as $part) {
        $data = [
            $part->getEvent()->getNom(),
            $part->getDate(),
            $part->getPrenom(),
            $part->getNom(),
            $part->getTel(),
           
        ];

        // Set data starting from the second row
        $sheet->fromArray([$data], null, 'A' . $sn);

        $sn++;
    }
    $sheet->getStyle('A1:D1')->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $writer = new Xlsx($spreadsheet);

    $fileName = 'parts.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);

     $writer->save($tempFile);
    
    return new BinaryFileResponse($tempFile, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => sprintf('inline; filename="%s"', $fileName),
    ]);
}

    #[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function newp(Request $request, EntityManagerInterface $entityManager,Security $s): Response
    {
        $user = $s->getUser();
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            if($user)
            {
                $participation->setUser($user);
            }
            $entityManager->persist($participation);
            $entityManager->flush();
            $this->addFlash('success', 'Le participation a été ajouté avec succès.');

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }
    #[Route('/participation/new/{eventId}', name: 'app_participation_newp', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $eventId,Security $s): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($eventId);

        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        $participation = new Participation();
        $participation->setEvent($event);
     
        $form = $this->createForm(ParticipationType1::class, $participation);
        $user = $s->getUser();
        if($user)
        {
            $participation->setUser($user);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          
            $entityManager->persist($participation);
            $entityManager->flush();
            $this->addFlash('success', 'Le participation a été ajouté avec succès.');

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/new1.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

   

    #[Route('/{id}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }
    #[Route('bb/{id}', name: 'app_participation_showb', methods: ['GET'])]
    public function showb(Participation $participation): Response
    {
        return $this->render('participation/showb.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }
        $this->addFlash('success', 'Le participation a été modifié avec succès.');

        return $this->renderForm('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Le participation a été suprimé avec succès.');

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('jnd/{id}', name: 'app_participation_deleteb', methods: ['POST'])]
    public function deleteb(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Le participation a été supprimé avec succès.');

        return $this->redirectToRoute('app_participation_indexb', [], Response::HTTP_SEE_OTHER);
    }
}