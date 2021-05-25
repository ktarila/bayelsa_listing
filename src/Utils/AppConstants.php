<?php

/*
 * This file is part of Rail Ticket Booking Application Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>.
 */

namespace App\Utils;

class AppConstants
{
    public function getIconArray(): array
    {
        return array_flip([
            'project-diagram' => '&#10070; project-diagram',
            'bell' => '&#xf0f3; bell',
            'building' => '&#xf1ad; building',
            'calendar' => '&#xf133; calendar',
            'check-circle' => '&#xf058; check-circle',
            'circle' => '&#xf111; circle',
            'star' => '&#xf005; star',
            'stop-circle' => '&#xf28d; stop-circle',
            'user' => '&#xf007; user',
            'window-close' => '&#xf410; window-close',
            'window-maximize' => '&#xf2d0; window-maximize ',
            'window-minimize' => '&#xf2d1; window-minimize',
            'window-restore' => '&#xf2d2; window-restore',
        ]);
    }
}
