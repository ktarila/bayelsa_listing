<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Twig;

use App\Repository\TagRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GetRandomTagsExtension extends AbstractExtension
{
    private $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'getRandomTags']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('random_tags', [$this, 'getRandomTags']),
        ];
    }

    public function getRandomTags()
    {
        return $this->tagRepository->findRandomTags();
    }
}
