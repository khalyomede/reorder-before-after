<?php

declare(strict_types=1);

use Khalyomede\ReorderBeforeAfter\Exceptions\InvalidApplyWithCallbackException;
use Khalyomede\ReorderBeforeAfter\Item;
use Khalyomede\ReorderBeforeAfter\Listing;
use Khalyomede\ReorderBeforeAfter\Placement;
use Tests\Misc\Product;

test("can reorder string item before another one in the middle of the list", function (): void {
    $list = new Listing();
    $list->push(new Item("table", 1));
    $list->push(new Item("chair", 2));
    $list->push(new Item("book", 3));
    $list->push(new Item("bag", 4));

    $list->reorder($list->find("bag"), Placement::Before, $list->find("table"));

    expect($list->find("bag")->order)->toBe(1);
    expect($list->find("table")->order)->toBe(2);
    expect($list->find("chair")->order)->toBe(3);
    expect($list->find("book")->order)->toBe(4);
});

test("can reorder string item after another one in the middle of the list", function (): void {
    $list = new Listing();
    $list->push(new Item("bag", 1));
    $list->push(new Item("table", 2));
    $list->push(new Item("chair", 3));
    $list->push(new Item("book", 4));

    $list->reorder($list->find("table"), Placement::After, $list->find("chair"));

    expect($list->find("bag")->order)->toBe(1);
    expect($list->find("chair")->order)->toBe(2);
    expect($list->find("table")->order)->toBe(3);
    expect($list->find("book")->order)->toBe(4);
});

test("can reorder string item before another one in the end of the list", function (): void {
    $list = new Listing();
    $list->push(new Item("bag", 1));
    $list->push(new Item("book", 2));
    $list->push(new Item("chair", 3));
    $list->push(new Item("table", 4));

    $list->reorder($list->find("bag"), Placement::Before, $list->find("chair"));

    expect($list->find("book")->order)->toBe(1);
    expect($list->find("bag")->order)->toBe(2);
    expect($list->find("chair")->order)->toBe(3);
    expect($list->find("table")->order)->toBe(4);
});

test("can reorder string item after another one in the end of the list", function (): void {
    $list = new Listing();
    $list->push(new Item("bag", 1));
    $list->push(new Item("book", 2));
    $list->push(new Item("chair", 3));
    $list->push(new Item("table", 4));

    $list->reorder($list->find("book"), Placement::After, $list->find("table"));

    expect($list->find("bag")->order)->toBe(1);
    expect($list->find("chair")->order)->toBe(2);
    expect($list->find("table")->order)->toBe(3);
    expect($list->find("book")->order)->toBe(4);
});

test("can reorder string item before another one in the begining of the list", function (): void {
    $list = new Listing();
    $list->push(new Item("bag", 1));
    $list->push(new Item("book", 2));
    $list->push(new Item("chair", 3));
    $list->push(new Item("table", 4));

    $list->reorder($list->find("chair"), Placement::Before, $list->find("bag"));

    expect($list->find("chair")->order)->toBe(1);
    expect($list->find("bag")->order)->toBe(2);
    expect($list->find("book")->order)->toBe(3);
    expect($list->find("table")->order)->toBe(4);
});

test("can reorder string item after another one in the begining of the list", function (): void {
    $list = new Listing();
    $list->push(new Item("bag", 1));
    $list->push(new Item("book", 2));
    $list->push(new Item("chair", 3));
    $list->push(new Item("table", 4));

    $list->reorder($list->find("chair"), Placement::After, $list->find("bag"));

    expect($list->find("bag")->order)->toBe(1);
    expect($list->find("chair")->order)->toBe(2);
    expect($list->find("book")->order)->toBe(3);
    expect($list->find("table")->order)->toBe(4);
});

test("does nothing if reordering string item before itself", function (): void {
    $list = new Listing();
    $list->push(new Item("bag", 1));
    $list->push(new Item("book", 2));
    $list->push(new Item("chair", 3));
    $list->push(new Item("table", 4));

    $list->reorder($list->find("bag"), Placement::Before, $list->find("bag"));

    expect($list->find("bag")->order)->toBe(1);
    expect($list->find("book")->order)->toBe(2);
    expect($list->find("chair")->order)->toBe(3);
    expect($list->find("table")->order)->toBe(4);
});

test("does nothing if reordering string item after itself", function (): void {
    $list = new Listing();
    $list->push(new Item("bag", 1));
    $list->push(new Item("book", 2));
    $list->push(new Item("chair", 3));
    $list->push(new Item("table", 4));

    $list->reorder($list->find("bag"), Placement::After, $list->find("bag"));

    expect($list->find("bag")->order)->toBe(1);
    expect($list->find("book")->order)->toBe(2);
    expect($list->find("chair")->order)->toBe(3);
    expect($list->find("table")->order)->toBe(4);
});

test("can find object item", function (): void {
    $bags = new Product(name: "bags", quantity: 15, unitPrice: 149.99, order: 1);
    $tables = new Product(name: "tables", quantity: 4, unitPrice: 89.99, order: 2);
    $chairs = new Product(name: "chairs", quantity: 1, unitPrice: 399.99, order: 3);

    $list = new Listing();
    $list->push(new Item($bags, 1));
    $list->push(new Item($tables, 2));
    $list->push(new Item($chairs, 3));

    expect($list->find($tables)->order)->toBe(2);
});

test("can create a listing from an array", function (): void {
    $listing = Listing::from([
        new Item("bag", 1),
        new Item("book", 2),
        new Item("chair", 3),
        new Item("table", 4),
    ]);

    expect($listing->find("bag")->order)->toBe(1);
    expect($listing->find("book")->order)->toBe(2);
    expect($listing->find("chair")->order)->toBe(3);
    expect($listing->find("table")->order)->toBe(4);
});

test("throws an exception if creating a listing from an array of non Item", function (): void {
    /** @phpstan-ignore-next-line */
    expect(fn (): Listing => Listing::from([
        ["bag", 1],
        ["book", 2],
        ["chair", 3],
        ["table", 4],
    ]))->toThrow(TypeError::class, 'Khalyomede\ReorderBeforeAfter\Listing::Khalyomede\ReorderBeforeAfter\{closure}(): Argument #1 ($item) must be of type Khalyomede\ReorderBeforeAfter\Item, array given');
});

test("can get back all items from a listing", function (): void {
    $listing = Listing::from([
        new Item("bag", 1),
        new Item("book", 2),
        new Item("chair", 3),
        new Item("table", 4),
    ]);

    $items = $listing->all();

    expect($items[0])->toBe("bag");
    expect($items[1])->toBe("book");
    expect($items[2])->toBe("chair");
    expect($items[3])->toBe("table");
});

test("can apply the order using a callback", function (): void {
    $bag = new Product(name: "bag", quantity: 12, unitPrice: 19.99, order: 1);
    $book = new Product(name: "book", quantity: 12, unitPrice: 99.99, order: 2);
    $chair = new Product(name: "chair", quantity: 12, unitPrice: 39.99, order: 3);
    $table = new Product(name: "table", quantity: 12, unitPrice: 29.99, order: 4);

    $listing = Listing::from([
        new Item($bag, 1),
        new Item($book, 2),
        new Item($chair, 3),
        new Item($table, 4),
    ]);

    $listing->applyWith(fn (Item $item): int => $item->value->order = $item->order);
    $listing->reorder($listing->find($table), Placement::Before, $listing->find($chair));

    expect($bag->order)->toBe(1);
    expect($book->order)->toBe(2);
    expect($table->order)->toBe(3);
    expect($chair->order)->toBe(4);
});

test("throws exception if the apply with a callback without parameters", function (): void {
    $listing = new Listing();

    expect(function () use ($listing): void {
        $listing->applyWith(fn (): int => 1);
    })->toThrow(InvalidApplyWithCallbackException::class, "Your callback must have exactly one parameter");
});

test("throws exception if the apply with a callback without type hint", function (): void {
    $listing = new Listing();

    expect(function () use ($listing): void {
        $listing->applyWith(fn ($item): int => 1);
    })->toThrow(InvalidApplyWithCallbackException::class, "Your callback must be type hinted with " . Item::class);
});

test("throws exception if the apply with a callback without Item type hint", function (): void {
    $listing = new Listing();

    expect(function () use ($listing): void {
        $listing->applyWith(fn (Product $item): int => 1);
    })->toThrow(InvalidApplyWithCallbackException::class, "Your callback must be type hinted with " . Item::class);
});
