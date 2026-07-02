<?php

declare(strict_types=1);

namespace App\Enum;

enum PlaceType: string
{
    case Monument = 'monument';
    case Restaurant = 'restaurant';
    case Shop = 'shop';
    case Hotel = 'hotel';
    case Museum = 'museum';
    case Park = 'park';
    case Transport = 'transport';
    case Other = 'other';
}
