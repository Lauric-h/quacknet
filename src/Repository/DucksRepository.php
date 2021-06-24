<?php

namespace App\Repository;

use App\Entity\Ducks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method Ducks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ducks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ducks[]    findAll()
 * @method Ducks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DucksRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ducks::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Ducks) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findNotDeleted(): ?array {
        $query = $this->createQueryBuilder('d')
            ->where('d.deleted = false')
            ->getQuery();

        return $query->execute();
    }


}
