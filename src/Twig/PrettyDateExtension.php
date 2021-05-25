<?php

/*
 * This file is part of Rail Ticket Booking Application Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>.
 */

namespace App\Twig;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PrettyDateExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('ago_date', [$this, 'ago_date']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ago_date', [$this, 'ago_date']),
        ];
    }

    public function ago_date($date)
    {
        if (null === $date) {
            return '';
        }
        $dt = Carbon::parse($date);

        return $dt->diffForHumans();
    }
}
