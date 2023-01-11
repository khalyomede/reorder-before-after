<?php

declare(strict_types=1);

namespace Khalyomede\ReorderBeforeAfter;

enum Placement
{
    case Before;
    case After;

    public function isBefore(): bool
    {
        return $this === self::Before;
    }
}
