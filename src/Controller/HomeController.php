<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface as Paginator;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository $pr, Paginator $paginator, Request $rq): Response
    {
        $produits = $pr->findAll();
        $page = $rq->query->get("page", 1);
        // La fonction paginate va filtrer les produits à afficher selon le numéro de page demandé
            // 1e argument : la liste totate des produit à afficher
            // 2e argument : le numéro de la page actuelle
            // 3e argument : le nombre de produits affichés par page
        $produits = $paginator->paginate($produits, $page, 6);
        return $this->render('home/index.html.twig', [
            "produits" => $produits,
        ]);
    }
}
