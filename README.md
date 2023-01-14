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

- PHP >= 8.1
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
- [4. Create a listing from an array](#4-create-a-listing-from-an-array)
- [5. Getting all items from a listing](#5-getting-all-items-from-a-listing)
- [6. Use a callback to apply the order on your value](#6-use-a-callback-to-apply-the-order-on-your-value)
- [7. Create a listing from values and specify how to retrieve the order using a callback](#7-create-a-listing-from-values-and-specify-how-to-retrieve-the-order-using-a-callback)

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

$listing->reorder("book", Placement::Before, "table");

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

### 4. Create a listing from an array

In this example, we will create the listing from an array instead of manually pushing items.

```php
use Khalyomede\ReorderBeforeAfter\Listing;

$listing = Listing::from([
    new Item("bag", 1),
    new Item("chair", 2),
    new Item("book", 3),
    new Item("table", 4),
]);

$listing->reorder("bag", Placement::After, "book");

assert($listing->find("bag")->order === 3);
```

### 5. Getting all items from a listing

In this example, we will get all items from a listing. Useful if you want to perform some task after reordering your items.

```php
use Khalyomede\ReorderBeforeAfter\Listing;

$listing = Listing::from([
    new Item("bag", 1),
    new Item("chair", 2),
    new Item("book", 3),
    new Item("table", 4),
]);

$products = $listing->all();

foreach ($products as $product) {
    echo $product; // "bag" or "chair" or "book" or "table"
}
```

### 6. Use a callback to apply the order on your value

In this example, we will instruct the listing how it should set the order on our values. Useful if our values are objects holding their order. This saves you from looping again on all your objects.

This examples features an hypothetical `Product` [Eloquent](https://laravel.com/docs/master/eloquent) model.

```php
use App\Models\Product;
use Khalyomede\ReorderBeforeAfter\Listing;

$items = Product::all()->map(fn (Product $product): Item => new Item($product, $product->order));
$listing = Listing::from($items);

$listing->applyWith(function (Item $item): void {
    $item->value->order = $item->order;
    $item->value->save();
});
```

### 7. Create a listing from values and specify how to retrieve the order using a callback

In this example, we will see how to create a listing out of an array of values, and using the second parameter to specify how to get the order from these values.

Useful if you have objects that already hold their own order, and you do not want to loop from them and create the `Item` by hand.

```php
use App\Models\Product;
use Khalyomede\ReorderBeforeAfter\Listing;

$products = Product::all();
$listing = Listing::outOf($products, fn (Product $product): Item => new Item($product, $product->order));
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

Or

```bash
composer run all
```
