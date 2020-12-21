<?php

declare(strict_types=1);

namespace loophp\collection\Operation;

use ArrayIterator;
use Closure;
use EmptyIterator;
use Generator;
use Iterator;

use function count;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 */
final class Chunk extends AbstractOperation
{
    /**
     * @psalm-return Closure(int...): Closure(Iterator<TKey, T>): Generator<int, list<T>>
     */
    public function __invoke(): Closure
    {
        return
            /**
             * @psalm-return Closure(Iterator<TKey, T>): Generator<int, list<T>>
             */
            static fn (int ...$sizes): Closure =>
                /**
                 * @psalm-param Iterator<TKey, T> $iterator
                 *
                 * @psalm-return Generator<int, list<T>>
                 */
                static function (Iterator $iterator) use ($sizes): Generator {
                    /** @psalm-var Iterator<int, int> $sizesIterator */
                    $sizesIterator = Cycle::of()(new ArrayIterator($sizes));
                    $sizesIterator->rewind();

                    $values = [];

                    for (; $iterator->valid(); $iterator->next()) {
                        $current = $iterator->current();

                        if (0 >= $sizesIterator->current()) {
                            return new EmptyIterator();
                        }

                        if (count($values) !== $sizesIterator->current()) {
                            $values[] = $current;

                            continue;
                        }

                        $sizesIterator->next();

                        yield $values;

                        $values = [$current];
                    }

                    return yield $values;
                };
    }
}
