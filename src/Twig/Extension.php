<?php 

namespace App\Twig;

use App\Entity\Membre;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extension extends AbstractExtension {
    public function autorisations(Membre $membre)
    {
        $autorisations = "";
        foreach( $membre->getRoles() as $role){
            $autorisations .= $autorisations ? ", " : "";
            switch ($role) {
                case "ROLE_ADMIN":
                    $autorisations .= "Gérant";
                    break;
                
                case "ROLE_VENDEUR":
                    $autorisations .= "Vendeur";
                    break;

                default:
                    $autorisations .= "Membre";
                    break;
            }
        }
        return $autorisations;
    }

    // Les filtres que l'on veut ajouter doivent être renvoyés dans un array par la fonction getFilters
    // chaque valeur de cet array est un objet de la classe TwigFilter
    // Les argument du constructeur de TwigFilter :
        // 1er : le nom du filtre à utiliser dans les fichiers Twig
        // 2eme : la fonction qui est déclaré dans cette classe
    // [$this, nom_de_la_fonction_dans_la_classe]
    public function getFilters()
    {
        return [
            new TwigFilter("autorisations", [$this, "autorisations"]),
            new TwigFilter("salutations", [$this, "salutations"])
        ];
    }

    public function getFunctions(){
        return [
            new TwigFunction("autorisations", [$this, "autorisations"])
        ];
    }

    public function salutations(Membre $membre) 
    {
        $datetime = new DateTime();
        if(!empty($membre->getPrenom()) && !empty($membre->getNom())) {
            $salutation = "Bonjour ".$membre->getPrenom()." ".$membre->getNom()." Nous somme le ".$datetime->format("d/m/Y");
        } else {
            $salutation = "Bonjour email@email.com, nous somme le ".$datetime->format("d/m/Y");
        }
        return $salutation;
    }
}