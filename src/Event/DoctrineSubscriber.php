<?php


namespace App\Event;


use App\Entity\Quack;
use App\HttpSender;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DoctrineSubscriber implements EventSubscriber
{

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::preRemove,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void {
        $entity = $args->getObject();
        if (!$entity instanceof Quack) {
            dd("non");
            return;
        }
        $sender = new HttpSender();
        $sender->sendData($entity);
    }

    public function postUpdate(LifecycleEventArgs $args): void {
        $entity = $args->getObject();
        if ($entity instanceof Quack) {
            $sender = new HttpSender();
            $sender->updateData($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getObjectManager();
        if (!$entity instanceof Quack) {
            return;
        }
        // http sender function send deletion
    }
}