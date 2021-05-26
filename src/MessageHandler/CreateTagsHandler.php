<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\MessageHandler;

use App\Entity\Advert;
use App\Entity\Tag;
use App\Message\CreateTags;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateTagsHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateTags $createTags)
    {
        $this->addTags($createTags);
    }

    private function addTags($createTags)
    {
        $this->entityManager->clear();
        $advert = $this->entityManager->getRepository(Advert::class)
            ->find($createTags->getAdvertId())
        ;

        if (null !== $advert) {
            // get tags from description
            preg_match_all('/#(\w+)/', $advert->getDescription(), $matches);
            // preg_match_all('/(#\w+)/', $msg, $matches);
            $tagRepo = $this->entityManager->getRepository(Tag::class);

            foreach ($matches[0] as $hashtag) {
                $tag = $tagRepo->findOneBy(['name' => $hashtag]);
                if (null === $tag) {
                    $tag = new Tag();
                    $tag->setName($hashtag);
                    $this->entityManager->persist($tag);
                }
                $advert->addTag($tag);
                $this->entityManager->flush();
            }
        }
    }
}
