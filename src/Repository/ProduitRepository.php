<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

/*
    public function findByImage($id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
                'SELECT i
                FROM App\Entity\Image i
                INNER JOIN App\Entity\Presenter pre ON i.id = pre.idImage
                INNER JOIN App\Entity\Produit p ON pre.idProduit = p.id
                WHERE p.id = :id'
            )->setParameter('id', $id);
        return $query->getResult();
    }*/


    public function findOneBySomeField($value): ?Image
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.idProduit = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
