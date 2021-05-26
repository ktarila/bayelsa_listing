<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Message;

class CreateTags
{
    private $advertId;

    public function __construct(string $advertId)
    {
        $this->advertId = $advertId;
    }

    public function getAdvertId(): string
    {
        return $this->advertId;
    }
}
