<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
/*----------*/
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig');
    }
    //MANAGE PRODUCTS
    #[Route('/manage_products', name: 'manage_products')]
    public function Manager_products(): Response
    {
        return $this->render('admin/manage_products.html.twig');
    }
    //MANAGE EVENTS
    #[Route('/manage_events', name: 'manage_events')]
    public function manage_events(): Response
    {
        return $this->render('admin/manage_events.html.twig');
    }
    //ADMIN MANAGER DASHBOARD
    #[Route('/admin', name: 'app_admin')]
    public function indexadmin(): Response
    {
        return $this->render('admin/index.html.twig');
    }
    //CLIENT
    #[Route('/client', name: 'app_client')]
    public function indexclient(): Response
    {
        return $this->render('client/index.html.twig');
    }

    

/*-----------*/

}
