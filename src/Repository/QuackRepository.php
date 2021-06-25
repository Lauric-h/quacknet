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
    /**
     * QuackRepository constructor.
     * @param ManagerRegistry $registry
     */
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


    /** Fetch all quacks not deleted
     * @return array|null
     */
    public function findNotDeleted(): ?array
    {
        $query = $this->createQueryBuilder('q')
            ->where('q.deleted = 0')
            ->getQuery();

        return $query->execute();
    }

    public function findByAuthor(string $author)
    {
        $query = $this->createQueryBuilder('q')
            ->addSelect('d')
            ->innerJoin('q.duck', 'd')
            ->where('d.username LIKE :author')
            ->setParameter('author', '%'.$author.'%')
            ->getQuery();

        return $query->execute();
    }

    public function findByTag($tag) {
        $query = $this->createQueryBuilder('q')
            ->join('q.tags', 't')
            ->where('t.name LIKE :tag')
            ->setParameter('tag', '%'.$tag.'%')
            ->getQuery();

        return $query->execute();
    }

}
