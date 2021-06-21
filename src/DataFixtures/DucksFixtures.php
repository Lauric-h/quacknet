<?php

namespace App\DataFixtures;

use App\Entity\Ducks;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DucksFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $ducks = new Ducks();
        $ducks->setEmail("daf@quack.fr");
        $ducks->setUsername("Daffy");
        $ducks->setLastname("Donald");
        $ducks->setFirstname("Duck");
        $ducks->setPassword($this->passwordHasher->hashPassword(
            $ducks,
            'pass'
        ));
        $manager->persist($ducks);

        $ducks1 = new Ducks();
        $ducks1->setEmail("kuikui@quack.fr");
        $ducks1->setUsername("Kui");
        $ducks1->setLastname("Kuikui");
        $ducks1->setFirstname("Koukou");
        $ducks1->setPassword($this->passwordHasher->hashPassword(
            $ducks1,
            'pass2'
        ));


        $manager->persist($ducks1);
        $manager->flush();
    }
}
