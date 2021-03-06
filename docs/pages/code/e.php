<?php

declare(strict_types=1);

include __DIR__ . '/../../../vendor/autoload.php';

use loophp\collection\Collection;

$multiplication = static function (float $value1, float $value2): float {
    return $value1 * $value2;
};

$addition = static function (float $value1, float $value2): float {
    return $value1 + $value2;
};

$fact = static function (int $number) use ($multiplication): float {
    return Collection::range(1, $number + 1)
        ->foldleft(
            $multiplication,
            1
        )
        ->current();
};

$e = static function (int $value) use ($fact): float {
    return $value / $fact($value);
};

$listInt = static function (int $init, callable $succ): Generator {
    yield $init;

    while (true) {
        yield $init = $succ($init);
    }
};

$naturals = $listInt(1, static function (int $n): int {
    return $n + 1;
});

$number_e_approximation = Collection::fromIterable($naturals)
    ->map($e)
    ->until(static function (float $value): bool {
        return 10 ** -12 > $value;
    })
    ->foldleft($addition, 0)
    ->current();

var_dump($number_e_approximation); // 2.718281828459
