<?php

declare(strict_types=1);

namespace App\Enum;

enum TicketType: string
{
    case Entry = 'entry';
    case Boarding = 'boarding';
    case Reservation = 'reservation';
    case Voucher = 'voucher';
    case Pass = 'pass';
}
