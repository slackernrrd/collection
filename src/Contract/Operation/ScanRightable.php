<?php

declare(strict_types=1);

namespace loophp\collection\Contract\Operation;

use loophp\collection\Contract\Collection;

/**
 * @psalm-template TKey
 * @psalm-template TKey of array-key
 * @psalm-template T
 */
interface ScanRightable
{
    /**
     * @param mixed $initial
     * @psalm-param T|null $initial
     *
     * @psalm-return \loophp\collection\Collection<TKey, T>
     */
    public function scanRight(callable $callback, $initial = null): Collection;
}
