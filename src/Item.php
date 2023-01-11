<?php

declare(strict_types=1);

namespace Khalyomede\ReorderBeforeAfter;

final class Item
{
    public function __construct(
        public readonly mixed $value,
        public int $order,
    ) {
    }
}
