<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 50; $i++) {
            $produit = new Produit;
            $produit->setTitre("produit$i");
            $produit->setStock(rand(1, 100));
            $produit->setReference("REF00$i");
            $produit->setPrix(rand(1, 500));
            $manager->persist($produit);
        }

        $manager->flush();
    }
}
