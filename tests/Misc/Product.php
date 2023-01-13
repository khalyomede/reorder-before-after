<?php

declare(strict_types=1);

namespace Tests\Misc;

final class Product
{
    public function __construct(
        public readonly string $name,
        public readonly int $quantity,
        public readonly float $unitPrice,
        public int $order,
    ) {
    }
}
