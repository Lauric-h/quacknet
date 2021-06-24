<?php

namespace App\Repository;

use App\Entity\Quack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quack|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quack|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quack[]    findAll()
 * @method Quack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quack::class);
    }

    // /**
    //  * @return Quack[] Returns an array of Quack objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findNotDeleted(): ?array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT q FROM App\Entity\Quack q WHERE q.deleted = 0'
        );
        return $query->getResult();
    }

    public function findByWord(string $key) {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT q, d 
             FROM App\Entity\Quack q 
             INNER JOIN q.duck d
             WHERE d.username LIKE :key'
        )->setParameter('key', '%'.$key.'%');
        return $query->getResult();

//        $builder = $this->createQueryBuilder('q')
//            ->where('q.duck LIKE :key')
//            ->setParameter('key', '%'.$key.'%');
//
//        $query = $builder->getQuery();
//        dd($query->execute());
//        return $query->execute();
    }

}
