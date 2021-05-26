<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;

class MailSubscriber implements EventSubscriberInterface
{
    public function onMessageEvent(MessageEvent $event)
    {
        $msg = $event->getMessage()->from('donotreply@buyersandsellers.ng');
        $event->setMessage($msg);
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessageEvent',
        ];
    }
}
