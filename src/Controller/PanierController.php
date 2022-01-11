<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Detail;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(SessionInterface $session): Response
    {
        return $this->render('panier/index.html.twig', [
            'panier' => $session->get('panier', []),
        ]);
    }

    // Classe Session : méthodes
    // get    : pour récupérer un indice de $_SESSION
    // set    : pour définir   un indice de $_SESSION
    // remove : pour supprimer un indice de $_SESSION 

    //Objet de la classe Request : contient toutes le valeurs des superglobales de PHP ($_GET, $_POST...)

    #[Route('/ajouter-au-panier/{id}', name: 'panier_ajouter')]
    public function ajouter(SessionInterface $session, Produit $produit, Request $rq): Response
    {
        $qte = $rq->query->get("qte", 1);
        //Pour pas que le panier soit null avant le premier article dans le get en deuxieme arg on le défini comme array
        $panier = $session->get("panier", []);
        $produitExiste = false;
        foreach ($panier as $indice => $ligne) {
            if ($produit->getId() == $ligne["produit"]->getId()) {
                $panier[$indice]["quantite"] += $qte;
                $produitExiste = true;
            }
        }

        if (!$produitExiste) {
            $panier[] = ["produit" => $produit, "quantite" => $qte];
        }
        $session->set("panier", $panier);
        return $this->redirectToRoute("home");
    }

    #[Route('/vider-le-panier', name: 'panier_vider')]
    public function vider(SessionInterface $session): Response
    {
        $session->remove("panier");
        return $this->redirectToRoute("panier");
    }

    #[Route('/valider-le-panier', name: 'panier_valider')]
    #[IsGranted("ROLE_USER")]
    public function valider(SessionInterface $session, EntityManagerInterface $em, ProduitRepository $pr)
    {
        $panier = $session->get("panier", []);
        if ($panier) {
            $cmd = new Commande;
            $montant = 0;
            $cmd->setEtat("en attente");
            $cmd->setDateEnregistrement(new \DateTime());
            // Pour récupérer l'utilisateur connecté $this->getUser() peu importe le vrai nom de la class (ici membre)
            $cmd->setMembre($this->getUser());
            foreach ($panier as $ligne) {
                $detail = new Detail;
                $detail->setCommande($cmd);
                $detail->setQuantite($ligne['quantite']);
                // $detail->setProduit($ligne['produit']);
                /* Le produit du panier est récupéré dans la session. Il a donc été sérialisé (= transformé en string).
                    Pour l'EntityManager, cet objet de la class Entity\Produit est donc un nouveau produit, pas un produit récupéré dans la bdd.
                    Quand flush() sera executé un nouveau produit sera ajouté dans la bdd, si on utilise le produit de la session (comme dans le commentaire ci-dessus). 
                    C'est pourquoi on récupère à nouveau le produit avec le ProduitRepository avant de l'affecter à l'objet Detail.
                */
                $produit = $pr->find($ligne['produit']->getId());
                $detail->setProduit($produit);
                $detail->setPrix($produit->getPrix());
                $montant += $produit->getPrix() * $ligne['quantite'];
                $produit->setStock($produit->getStock() - $ligne['quantite']);
                $em->persist($detail);
            }
            $cmd->setMontant($montant);
            $em->persist($cmd);
            $em->flush();
            $session->remove("panier");
            return $this->redirectToRoute("espace");
        }
    }
}
