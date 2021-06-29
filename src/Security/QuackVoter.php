<?php


namespace App\Security;


use App\Entity\Ducks;
use App\Entity\Quack;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class QuackVoter extends Voter
{
    const DELETE = 'delete';
    const EDIT = 'edit';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
       if (!in_array($attribute, [self::DELETE, self::EDIT])) {
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

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($quack, $duck);
            case self::EDIT:
                return $this->canEdit($quack, $duck);
        }

//        if ($attribute == self::DELETE) {
//            return $this->canDelete($quack, $duck);
//        }
//        if ($attribute == self::EDIT) {
//            return $this->canEdit($quack, $duck);
//        }

        throw new \LogicException('You should not see this');
    }

    private function canDelete(Quack $quack, Ducks $duck): bool {
        if ($duck === $quack->getDuck() || $duck === $quack->getParent()->getDuck()){
            return true;
        }
        return false;
    }

    private function canEdit(Quack $quack, Ducks $duck): bool {
//        return $duck === $quack->getOwner();
        if ($duck === $quack->getDuck()){
            return true;
        }
        return false;
    }

}