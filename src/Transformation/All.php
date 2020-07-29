<?php

declare(strict_types=1);

namespace loophp\collection\Transformation;

use loophp\collection\Contract\Transformation;
use loophp\collection\Iterator\IterableIterator;

/**
 * @template TKey
 * @psalm-template TKey of array-key
 * @template T
 *
 * @implements Transformation<TKey, T>
 */
final class All implements Transformation
{
    /**
     * @return array<TKey, T>
     * @phpstan-return array<TKey, T>
     * @psalm-return array<TKey, T>
     */
    public function __invoke(iterable $collection): array
    {
        return iterator_to_array(new IterableIterator($collection));
    }
}
