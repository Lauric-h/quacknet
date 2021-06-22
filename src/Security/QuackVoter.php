<?php


namespace App\Security;


use App\Entity\Ducks;
use App\Entity\Quack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QuackVoter extends \Symfony\Component\Security\Core\Authorization\Voter\Voter
{
    const DELETE = 'delete';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
       if (!in_array($attribute, [self::DELETE])) {
           return false;
       }

       if (!$subject instanceof Quack) {
           return false;
       }

       return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $duck = $token->getUser();

        if(!$duck instanceof Ducks) {
            return false;
        }

        $quack = $subject;
        if ($attribute == self::DELETE) {
            return $this->canDelete($quack, $duck);
        }

        throw new \LogicException('You should not see this');
    }

    private function canDelete(Quack $quack, Ducks $duck): bool {
        return $duck === $quack->getDuck();
    }

}