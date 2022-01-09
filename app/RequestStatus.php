<?php

namespace App;

use Konekt\Enum\Enum;

class RequestStatus extends Enum
{
    const __DEFAULT = self::PENDING;

    const PENDING   = 0;
    const FAILED    = 1;
    const COMPLETED = 2;
}
