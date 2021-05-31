<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\MessageHandler;

use App\Entity\Advert;
use App\Message\UpdateAdvertCommentCount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateAdvertCommentCountHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(UpdateAdvertCommentCount $updateAdvertCommentCount)
    {
        $this->updateAdvertCommentCount($updateAdvertCommentCount);
    }

    private function updateAdvertCommentCount($updateAdvertCommentCount)
    {
        $this->entityManager->clear();
        $advertRepository = $this->entityManager->getRepository(Advert::class);
        $advert = $advertRepository->find($updateAdvertCommentCount->getAdvertId())
        ;

        if (null !== $advert) {
            $commentCount = $advertRepository->countComments($advert);
            $advert->setCommentCount($commentCount);
            $this->entityManager->persist($advert);
            $this->entityManager->flush();
        }
    }
}
