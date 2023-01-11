<?php

declare(strict_types=1);

namespace Khalyomede\ReorderBeforeAfter;

use Khalyomede\ReorderBeforeAfter\Exceptions\ItemNotFoundException;
use Khalyomede\ReorderBeforeAfter\Exceptions\TooManyItemsException;

final class Listing
{
    /**
     * @var array<Item>
     */
    private array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function push(Item $item): void
    {
        $this->items[] = $item;
    }

    public function reorder(Item $item, Placement $placement, Item $reference): void
    {
        $this->sortItems();

        if ($item !== $reference) {
            $this->removeItem($item);

            if ($placement->isBefore()) {
                $left = $this->itemsBefore($reference);
                $right = $this->itemsAfter($reference, included: true);
            } else {
                $left = $this->itemsBefore($reference, included: true);
                $right = $this->itemsAfter($reference);
            }

            $this->items = array_merge($left, [$item], $right);
        }

        $this->rewriteItemsIndexes();
    }

    public function find(mixed $value): Item
    {
        $items = array_filter($this->items, fn (Item $item): bool => $item->value === $value);
        $numberOfItemsFound = count($items);

        if ($numberOfItemsFound > 1) {
            throw new TooManyItemsException("$numberOfItemsFound items found with the same value");
        }

        if ($numberOfItemsFound === 0) {
            throw new ItemNotFoundException("No item match the value");
        }

        $item = array_shift($items);

        if ($item === null) {
            throw new ItemNotFoundException("No item match the value");
        }

        return $item;
    }

    private function sortItems(): void
    {
        usort($this->items, fn (Item $left, Item $right): int => $left->order <=> $right->order);
    }

    private function minimalIndex(): int
    {
        return min(array_map(fn (Item $item): int => $item->order, $this->items));
    }

    private function removeItem(Item $item): void
    {
        unset($this->items[$this->findItemIndex($item)]);

        $this->resetItemsIndexes();
    }

    private function resetItemsIndexes(): void
    {
        $this->items = array_values($this->items);
    }

    private function findItemIndex(Item $item): int
    {
        $item = array_search($item, $this->items, true);

        if (!is_int($item)) {
            throw new ItemNotFoundException("No item found match item value");
        }

        return $item;
    }

    /**
     * @return array<Item>
     */
    private function itemsBefore(Item $item, bool $included = false): array
    {
        $itemIndex = $this->findItemIndex($item);
        $pad = $included ? 1 : 0;

        return array_slice($this->items, 0, $itemIndex + $pad);
    }

    /**
     * @return array<Item>
     */
    private function itemsAfter(Item $item, bool $included = false): array
    {
        $itemIndex = $this->findItemIndex($item);
        $pad = $included ? 0 : 1;

        return array_slice($this->items, $itemIndex + $pad);
    }

    private function rewriteItemsIndexes(): void
    {
        $index = $this->minimalIndex();

        foreach ($this->items as $item) {
            $item->order = $index;

            $index += 1;
        }
    }
}
