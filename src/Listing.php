<?php

declare(strict_types=1);

namespace Khalyomede\ReorderBeforeAfter;

use Closure;
use Khalyomede\ReorderBeforeAfter\Exceptions\InvalidApplyWithCallbackException;
use Khalyomede\ReorderBeforeAfter\Exceptions\InvalidMatchWithCallbackException;
use Khalyomede\ReorderBeforeAfter\Exceptions\InvalidOutOfCallbackException;
use Khalyomede\ReorderBeforeAfter\Exceptions\ItemNotFoundException;
use Khalyomede\ReorderBeforeAfter\Exceptions\TooManyItemsException;
use ReflectionFunction;
use ReflectionNamedType;

final class Listing
{
    /**
     * @var array<Item>
     */
    private array $items;

    /**
     * @var Closure(Item): void
     */
    private Closure $applyWith;

    /**
     * @var Closure(mixed, mixed): bool
     */
    private Closure $matchWith;

    public function __construct()
    {
        $this->items = [];
        $this->applyWith = function (): void {
        };
        $this->matchWith = fn (mixed $left, mixed $right): bool => $left === $right;
    }

    /**
     * @param array<Item> $items
     */
    public static function from(array $items): self
    {
        $listing = new self();

        array_map(function (Item $item) use ($listing): void {
            $listing->push($item);
        }, $items);

        return $listing;
    }

    /**
     * @param array<mixed> $values
     * @param Closure(mixed): Item $callback
     *
     * @throws InvalidOutOfCallbackException
     */
    public static function outOf(array $values, Closure $callback): self
    {
        self::checkOutOfCallbackSignatureCorrect($callback);

        $items = array_map($callback, $values);

        return self::from($items);
    }

    public function push(Item $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @throws TooManyItemsException
     * @throws ItemNotFoundException
     */
    public function reorder(mixed $value, Placement $placement, mixed $reference): void
    {
        $this->sortItems();

        if ($value !== $reference) {
            $item = $this->find($value);
            $referenceItem = $this->find($reference);

            $this->removeItem($item);

            if ($placement->isBefore()) {
                $left = $this->itemsBefore($referenceItem);
                $right = $this->itemsAfter($referenceItem, included: true);
            } else {
                $left = $this->itemsBefore($referenceItem, included: true);
                $right = $this->itemsAfter($referenceItem);
            }

            $this->items = array_merge($left, [$item], $right);
        }

        $this->rewriteItemsIndexes();
    }

    /**
     * @throws TooManyItemsException
     * @throws ItemNotFoundException
     */
    public function find(mixed $value): Item
    {
        $items = array_filter($this->items, function (Item $item) use ($value): bool {
            $match = call_user_func_array($this->matchWith, [$item->value, $value]);

            assert(is_bool($match));

            return $match;
        });

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

    /**
     * @param Closure(Item): void $callback
     *
     * @throws InvalidApplyWithCallbackException
     */
    public function applyWith(Closure $callback): void
    {
        $reflection = new ReflectionFunction($callback);

        $parameters = $reflection->getParameters();

        if (count($parameters) !== 1) {
            throw new InvalidApplyWithCallbackException("Your callback must have exactly one parameter");
        }

        $parameter = array_shift($parameters);

        if (!($parameter->hasType())) {
            throw new InvalidApplyWithCallbackException("Your callback must be type hinted with " . Item::class);
        }

        $type = $parameter->getType();

        if (!($type instanceof ReflectionNamedType) || $type->getName() !== Item::class) {
            throw new InvalidApplyWithCallbackException("Your callback must be type hinted with " . Item::class);
        }

        $this->applyWith = $callback;
    }

    /**
     * @param Closure(mixed, mixed): bool $callback
     *
     * @throws InvalidMatchWithCallbackException
     */
    public function matchWith(Closure $callback): void
    {
        self::checkMatchWithCallbackSignature($callback);

        $this->matchWith = $callback;
    }

    /**
     * @return array<mixed>
     */
    public function all(): array
    {
        return array_values(array_map(fn (Item $item): mixed => $item->value, $this->items));
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

            call_user_func($this->applyWith, $item);

            $index += 1;
        }
    }

    private static function checkMatchWithCallbackSignature(Closure $callback): void
    {
        $reflection = new ReflectionFunction($callback);

        $returnType = $reflection->getReturnType();

        if (!($returnType instanceof ReflectionNamedType) || $returnType->getName() !== "bool") {
            throw new InvalidMatchWithCallbackException("Your callback must have a bool return type");
        }
    }

    private static function checkOutOfCallbackSignatureCorrect(Closure $callback): void
    {
        $reflection = new ReflectionFunction($callback);
        $returnType = $reflection->getReturnType();

        if (!($returnType instanceof ReflectionNamedType) || $returnType->getName() !== Item::class) {
            throw new InvalidOutOfCallbackException("Your callback must have an Item return type hint");
        }
    }
}
