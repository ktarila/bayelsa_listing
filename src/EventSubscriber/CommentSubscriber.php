<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\EventSubscriber;

use App\Entity\Comment;
use App\Message\UpdateAdvertCommentCount;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class CommentSubscriber implements EventSubscriber
{
    private $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateCommentCount($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->updateCommentCount($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateCommentCount($args);
    }

    private function updateCommentCount(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Comment
        ) {
            $advert = $entity->getAdvert();
            $this->eventBus->dispatch(new UpdateAdvertCommentCount($advert->getId()));
        }
    }
}
