<?php

namespace App\Controller;

use App\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_USER")]
class EspaceController extends AbstractController
{

    #[Route('/espace-membre', name: 'espace')]
    public function index(): Response
    {
        return $this->render('espace/index.html.twig', [
            'controller_name' => 'EspaceController',
        ]);
    }
    #[Route('espace-membre/detail-commande/{id}', name: 'espace_detail')]
    public function detail_commande(Commande $cmd)
    {

        $commandeUtilisateurConnecte = $this->getUser()->getCommandes();
        if ($this->isGranted("ROLE_ADMIN") || $commandeUtilisateurConnecte->contains($cmd)) {
            return $this->render('espace/detail_commande.html.twig', [
                'commande' => $cmd,
            ]);
        } else {
            $this->addFlash("danger", "Il semblerait que vous vous soyez trompé dans le numéro de commande");
            return $this->redirectToRoute("espace");
        }
    }
}
