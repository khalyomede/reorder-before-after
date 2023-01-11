<?php

declare(strict_types=1);

namespace Tests\Misc;

final readonly class Product
{
    public function __construct(
        public string $name,
        public int $quantity,
        public float $unitPrice,
    ) {
    }
}
