<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\EventListener;

use App\Entity\Advert;
use App\Entity\Upload;
use App\Message\CreateTags;
use App\Repository\UploadRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class AdvertListener
{
    private $bus;
    private $uploadRepository;

    public function __construct(MessageBusInterface $bus, UploadRepository $uploadRepository)
    {
        $this->bus = $bus;
        $this->uploadRepository = $uploadRepository;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->bus->dispatch(new CreateTags($entity->getId()));

        $this->attachUploads($entity);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->bus->dispatch(new CreateTags($entity->getId()));

        $this->attachUploads($entity);
    }

    private function attachUploads($entity)
    {
        if ($entity instanceof Advert) {
            // attach upload
            $this->uploadRepository->attachAdvert($entity);
        }
    }
}
