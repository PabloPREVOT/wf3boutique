<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'recherche')]
    public function index(Request $rq, ProduitRepository $pr): Response
    {
        $mot = $rq->query->get("mot");
        $produits = $pr->findProduitRecherche($mot);
        return $this->render('recherche/titre.html.twig', [
            'produits' => $produits,
            'mot' => $mot,
        ]);
    }

    #[Route('/recherche-par-prix', name: 'recherche_prix')]
    public function recherchePrix(Request $rq, ProduitRepository $pr): Response
    {
        $mot = $rq->query->get("mot");
        $produits = $pr->findProduitPrixRecherche($mot);
        return $this->render('recherche/prix.html.twig', [
            'produits' => $produits,
            'mot' => $mot,
        ]);
    }
}
