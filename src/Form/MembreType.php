<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $membre = $options["data"]; // $options["data"] permet de récupérer l'objet utilisé comme données du formulaire
        //                             c'est le 2e argument de createForm() utilisé dans le controleur
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "Gérant" => "ROLE_ADMIN",
                    "Vendeur" => "ROLE_VENDEUR",
                    "Membre" => "ROLE_USER"
                ],
                "multiple" => true,  // Obligatoirement true, parce que la propriété roles peut avoir plusieurs valeurs ( array)
                "expanded" => true, // true : case à cocher, false : select avec selection multiple possible
                "label"    => "Droits d'accès"
            ])
            ->add('password', PasswordType::class, [
                "mapped"    => false,
                "label"     => "Mot de passe",
                "required"  =>  $membre->getId() ? false : true,
            ])
            ->add('civilite', ChoiceType::class, [
                "choices" => [
                    "Mme"   => "f",
                    "M"     => "h",
                    "Autre" => "a"
                ],
                "multiple" => false,
                "expanded" => true // Ici comme multiple est à false ce sera des boutons radio
            ])
            ->add('nom')
            ->add('prenom')
            ->add('code_postal', TextType::class, [
                "label"     => "Code ostal",
                "constraints" => [
                    new Regex([
                        //"pattern" => "/^\d{5}$/" // Cette chaine de caractère signifie que entre les slashes de débuts et fin nous pouvons insérer uniquement 5 chiffres
                        // ^ : début de chaine de caractères
                        // $ : fin de chaine
                        // d : decimal
                        // {5} : 5 fois l'itération précédente donc ici 5 fois des décimales
                        "pattern" => "/^((0[1-9])|([1-8][0-9])|(9[0-8]))[0-9]{3}$/",  // Cette chaine de caractères sert à définir les limites d'entrées pour le code postale francais 
                        "message" => "Le code postal n'est pas valide",
                    ])
                ],
                "help" => "Le code postal doit comporter 5 chiffres"
            ])
            ->add('ville')
            ->add('adresse');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
        ]);
    }
}
