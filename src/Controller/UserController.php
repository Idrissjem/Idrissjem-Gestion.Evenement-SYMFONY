<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $currentPage = $request->query->getInt('page', 1);
        $pageSize = 10; // Number of items per page
        $paginatedData = $paginator->paginate(
            $userRepository->findAll(),
            $currentPage,
            $pageSize
        );
    
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'paginatedData' => $paginatedData
        ]);
    }
    
    
    #[Route('d/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function deletee(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
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
                 $user->setImage($newFilename);
             }
             $hashedPassword = hash('sha1', $user->getPassword());
             $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('registration/register.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('s/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('d/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(User1Type::class, $user);
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
     
                 $user->setImage($newFilename);
             }
             $hashedPassword = hash('sha1', $user->getPassword());
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('d/{id}/editf', name: 'app_user_editf', methods: ['GET', 'POST'])]
    public function editf(Request $request, User $user, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $form = $this->createForm(User1Type::class, $user);
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
     
                 $user->setImage($newFilename);
             }

             $hashedPassword = hash('sha1', $user->getPassword());
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            return $this->redirectToRoute('app_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editf.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


   
    #[Route('/banUser/{id}', name: 'ban_user', methods: ['GET', 'POST'])]
    public function banUser(Request $request, UserRepository $userRepository,int $id): Response
    {
        // Retrieve the search query from the request
        $user = $userRepository->find($id);
  
        // Perform the search operation based on the query
         $userRepository->banUnbanUser($user);
  
        // Return the search results as JSON response
        return $this->redirectToRoute('app_user_index');
    }
    #[Route('/search', name: 'search_users', methods: ['POST'])]
public function search(Request $request, UserRepository $userRepository): Response
{
    $searchText = $request->request->get('searchText');
    $users = $userRepository->findBySearchText($searchText); // Implement findBySearchText() method in UserRepository

    return $this->render('user/search_results.html.twig', [
        'users' => $users,
    ]);
}
#[Route('/search-and-sort', name: 'search_and_sort_users', methods: ['POST'])]
public function searchAndSort(Request $request, UserRepository $userRepository): Response
{
    $searchText = $request->request->get('searchText');
    $sortBy = $request->request->get('sortBy');

    if ($sortBy === 'name') {
        $users = $userRepository->findBySearchTextAndSortByName($searchText); // Implement findBySearchTextAndSortByName() method in UserRepository
    } else {
        $users = $userRepository->findBySearchText($searchText); // Implement findBySearchText() method in UserRepository
    }

    return $this->render('user/search_and_sort_results.html.twig', [
        'users' => $users,
    ]);
}

}
