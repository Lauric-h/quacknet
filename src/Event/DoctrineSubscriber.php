<?php


namespace App\Event;


use App\Entity\Quack;
use App\HttpSender;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
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
            return;
        }
        $sender = new HttpSender();
        $sender->sendData($entity);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function postUpdate(LifecycleEventArgs $args): void {
        $entity = $args->getObject();
        if ($entity instanceof Quack) {
            $sender = new HttpSender();

            //delete doc from ES
            if ($entity->getDeleted() === 1) {
                $sender->deleteData($entity->getId());
                return;
            }

            $sender->updateData($entity);
        }
    }
}