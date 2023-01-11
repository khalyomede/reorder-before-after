<?php

declare(strict_types=1);

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
    $bags = new Product(name: "bags", quantity: 15, unitPrice: 149.99);
    $tables = new Product(name: "tables", quantity: 4, unitPrice: 89.99);
    $chairs = new Product(name: "chairs", quantity: 1, unitPrice: 399.99);

    $list = new Listing();
    $list->push(new Item($bags, 1));
    $list->push(new Item($tables, 2));
    $list->push(new Item($chairs, 3));

    expect($list->find($tables)->order)->toBe(2);
});
