<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @return Produit[] Returns an array of Produit objects
     * SELECT p.* FROM produit WHERE p.titre LIKE '%$value%';
     */

    public function findProduitRecherche($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.titre LIKE :mot')
            ->setParameter('mot', "%$value%")
            ->orderBy('p.prix', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Produit[] Retourne les produits dont les prix sont inférieurs à l'argument
     * SELECT p.* FROM produit p WHERE p.prix < :mot ORDER BY p.prix DESC
     *
     */

    public function findProduitPrixRecherche($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.prix < :mot')
            ->setParameter('mot', $value)
            ->orderBy('p.prix', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
