# reorder-before-after

Reorder an item in an array before or after another.

## Summary

- [About](#about)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Examples](#examples)
- [Tests](#tests)

## About

I make a web app in which my users can reorder before or after another.

I did not find any package to do what I search for, and since I use it at multiple place on my app I figured I would open source it in the hope it can help simplify the life of another developer out there!

## Features

- Can reorder an item before or after another in a list of items
- Systematically "self-heals" the items orders by re-writing the index of all items starting from the minimum index

## Requirements

- PHP >= 8.2
- Composer

## Installation

In your terminal, on the root of your project folder, run:

```bash
composer require khalyomede/reorder-before-after
```

## Examples

- [1. Moving an item before another](#1-moving-an-item-before-another)
- [2. Create a list of items out of any type](#2-create-a-list-of-items-out-of-any-type)
- [3. Find an item by its value](#3-find-an-item-by-its-value)

### 1. Moving an item before another

In this example, we will move our book before our table.

```php
use Khalyomede\ReorderBeforeAfter\Item;
use Khalyomede\ReorderBeforeAfter\Listing;
use Khalyomede\ReorderBeforeAfter\Placement;

$listing = new Listing();

$listing->push(new Item("bag", 1));
$listing->push(new Item("chair", 2));
$listing->push(new Item("table", 3));
$listing->push(new Item("book", 4));

$listing->reorder($book, Placement::Before, $table);

assert($listing->find("bag")->order === 1);
assert($listing->find("chair")->order === 2);
assert($listing->find("book")->order === 3);
assert($listing->find("table")->order === 4);
```

### 2. Create a list of items out of any type

In this example, we will see that we can create a listing of anything, including objects.

```php
use Khalyomede\ReorderBeforeAfter\Item;
use Khalyomede\ReorderBeforeAfter\Listing;

final readonly class Product
{
    public function __construct(
        public string $name,
        public int $quantity,
        public float $unitPrice,
    ) {}
}

$bags = new Product("bag", 15, 149.99);
$tables = new Product("table", 4, 89.99);
$chairs = new Product("chairs", 1, 399.99);

$listing = new Listing();

$listing->push(new Item($bags, 1));
$listing->push(new Item($tables, 2));
$listing->push(new Item($chairs, 3));
```

### 3. Find an item by its value

In this example, we will see we can find an item by its "value" (the first parameter when creating a new instance of `Item`).

```php
use Khalyomede\ReorderBeforeAfter\Item;
use Khalyomede\ReorderBeforeAfter\Listing;
use Khalyomede\ReorderBeforeAfter\Placement;

$listing = new Listing();

$listing->push(new Item("bag", 1));
$listing->push(new Item("chair", 2));
$listing->push(new Item("table", 3));
$listing->push(new Item("book", 4));

echo $listing->find("bag"); // Item(value: "bag", order: 1)
```

If the method finds no item matching the value, a `ItemNotFoundException` will be thrown.

You can also search by anything that can be checked using `===`, including objects.

```php
use Khalyomede\ReorderBeforeAfter\Item;
use Khalyomede\ReorderBeforeAfter\Listing;
use Khalyomede\ReorderBeforeAfter\Placement;

final readonly class Product
{
    public function __construct(
        public string $name,
        public int $quantity,
        public float $unitPrice,
    ) {}
}

$bags = new Product("bag", 15, 149.99);
$tables = new Product("table", 4, 89.99);
$chairs = new Product("chairs", 1, 399.99);

$listing = new Listing();

$listing->push(new Item($bags, 1));
$listing->push(new Item($tables, 2));
$listing->push(new Item($chairs, 3));

echo $listing->find($bags); // Item(value: Product(name: "bag", quantity: 15, unitPrice: 149.99), order: 1)
```

## Tests

```bash
composer run test
composer run analyse
composer run lint
composer run check
composer run updates
composer run scan
```