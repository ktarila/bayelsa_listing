<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Utils;

class Helpers
{
    public function iterable_to_array(iterable $it): array
    {
        if (\is_array($it)) {
            return $it;
        }
        $ret = [];
        array_push($ret, ...$it);

        return $ret;
    }

    public function slugify($string)
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }
}
