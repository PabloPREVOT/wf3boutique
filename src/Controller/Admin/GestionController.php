<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionController extends AbstractController
{
    #[Route('/admin/gestion', name: 'admin_gestion')]
    public function index(): Response
    {
        return $this->render('admin/gestion/index.html.twig', [
            'controller_name' => 'GestionController',
        ]);
    }
}
