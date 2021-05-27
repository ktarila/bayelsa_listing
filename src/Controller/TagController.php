<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller;

use App\Repository\AdvertRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag')]
class TagController extends AbstractController
{
    #[Route('/{tagname}', name: 'advert_by_tag', methods: ['POST', 'GET'])]
    public function advertByTag($tagname, AdvertRepository $advertRepository, TagRepository $tagRepository): Response
    {
        $tag = $tagRepository->findOneByName("#{$tagname}");
        $adverts = [];
        if (null !== $tag) {
            $adverts = $advertRepository->findAll();
        }

        return $this->render('tag/by_tag.html.twig', [
            'tag' => $tagname,
            'adverts' => $adverts,
        ]);
    }
}
