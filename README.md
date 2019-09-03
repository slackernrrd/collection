[![Latest Stable Version](https://img.shields.io/packagist/v/drupol/collection.svg?style=flat-square)](https://packagist.org/packages/drupol/collection)
 [![GitHub stars](https://img.shields.io/github/stars/drupol/collection.svg?style=flat-square)](https://packagist.org/packages/drupol/collection)
 [![Total Downloads](https://img.shields.io/packagist/dt/drupol/collection.svg?style=flat-square)](https://packagist.org/packages/drupol/collection)
 [![Build Status](https://img.shields.io/travis/drupol/collection/master.svg?style=flat-square)](https://travis-ci.org/drupol/collection)
 [![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/drupol/collection/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/collection/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/drupol/collection/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/drupol/collection/?branch=master)
 [![Mutation testing badge](https://badge.stryker-mutator.io/github.com/drupol/collection/master)](https://stryker-mutator.github.io)
 [![License](https://img.shields.io/packagist/l/drupol/collection.svg?style=flat-square)](https://packagist.org/packages/drupol/collection)
 [![Say Thanks!](https://img.shields.io/badge/Say-thanks-brightgreen.svg?style=flat-square)](https://saythanks.io/to/drupol)
 [![Donate!](https://img.shields.io/badge/Donate-Paypal-brightgreen.svg?style=flat-square)](https://paypal.me/drupol)
 
# PHP Collection

## Description

A Collection is an object that can hold a list of items and do things with it.

This Collection class:
 * is [immutable](https://en.wikipedia.org/wiki/Immutable_object),
 * extendable,
 * leverage the power of PHP [generators](https://www.php.net/manual/en/class.generator.php) and [iterators](https://www.php.net/manual/en/class.iterator.php),
 * use [S.O.L.I.D principles](https://en.wikipedia.org/wiki/SOLID),
 * doesn't depends or require any other library or framework.

Except a few methods, most of methods are pure and returning a new Collection object.

This library has been inspired by the [Laravel Support Package](https://github.com/illuminate/support) and [Lazy.js](http://danieltao.com/lazy.js/).

## Requirements

* PHP >= 7.1.3

## Installation

```composer require drupol/collection```

## Usage

```php
<?php

declare(strict_types=1);

include 'vendor/autoload.php';

use drupol\collection\Collection;

// More examples...
$collection = Collection::with(['A', 'B', 'C', 'D', 'E']);

// Get the result as an array.
$collection
    ->all(); // ['A', 'B', 'C', 'D', 'E']

// Get the first item.
$collection
    ->first(); // A

// Append items.
$collection
    ->append('F', 'G', 'H')
    ->all(); // ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']

// Prepend items.
$collection
    ->prepend('1', '2', '3')
    ->all(); // ['1', '2', '3', 'A', 'B', 'C', 'D', 'E']

// Split a collection into chunks of a given size.
$collection
    ->chunk(2)
    ->map(function (Collection $collection) {return $collection->all();})
    ->all(); // [['A', 'B'], ['C', 'D'], ['E']]

// Merge items.
$collection
    ->merge([1, 2], [3, 4], [5, 6])
    ->all(); // ['A', 'B', 'C', 'D', 'E', 1, 2, 3, 4, 5, 6]

// Map data
$collection
    ->map(
        static function ($item, $key) {
            return sprintf('%s.%s', $item, $item);
        }
    )
    ->all(); // ['A.A', 'B.B', 'C.C', 'D.D', 'E.E']

// ::map() and ::walk() are not the same.
Collection::with(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'])
    ->map(static function ($item, $key) {return strtolower($item); })
    ->all(); // [0 => 'a', 1 => 'b', 2 => 'c', 3 = >'d', 4 => 'e']

Collection::with(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'])
    ->walk(static function ($item, $key) {return strtolower($item); })
    ->all(); // ['A' => 'a', B => 'b', 'C' => 'c', 'D' = >'d', 'E' => 'e']

// Infinitely loop over numbers, square them, filter those that are not divisible by 5, take the first 100 of them.
Collection::range(0, INF)
    ->map(static function ($number) {
        return $number ** 3;
    })
    ->filter(static function ($number) {
        return $number % 5;
    })
    ->limit(100)
    ->all(); // [1, 8, 27, ..., 1815848, 1860867, 1906624]

// Apply a callback to the values without altering the original object.
// If the callback returns false, then it will stop.
Collection::with(range('A', 'Z'))
    ->apply(static function ($item, $key) {
        echo strtolower($item);
    });

// The famous Fibonacci example:
Collection::with(
        static function($start = 0, $inc = 1) {
            yield $start;

            while(true)
            {
                $inc = $start + $inc;
                $start = $inc - $start;
                yield $start;
            }
        }

    )
    ->limit(10)
    ->all(); // [0, 1, 1, 2, 3, 5, 8, 13, 21, 34]

// or

Collection::iterate(
    static function($v) {
        return [$v[1], $v[0] + $v[1]];
    },
    1,1
    )
    ->limit(10)
    ->all(); // [0, 1, 1, 2, 3, 5, 8, 13, 21, 34]

// Find the golden ratio with Fibonacci
$result = Collection::iterate(
    static function($v) {
        return [$v[1], $v[0] + $v[1]];
    },
    1,1
    )
    ->map(static function($item) {return $item[0];})
    ->chunk(2)
    ->map(
        static function(ArrayIterator $item) {
            [$first, $second] = $item;

            return $second / $first;
        }
    )
    ->limit(100)
    ->last(); // 1.6180339887499

// Use an existing Generator as input data.
$readFileLineByLine = static function (string $filepath): \Generator {
    $fh = fopen($filepath, 'rb');
    $i = 0;

    while (!feof($fh)) {
        $line = '';

        while (PHP_EOL !== $chunk = fread($fh, 1)) {
            $line .= $chunk;
        }

        yield [
            'number' => $i++,
            'content' => $line
        ];
    }

    fclose($fh);
};

$hugeFile = __DIR__ .'/vendor/composer/autoload_static.php';

Collection::with($readFileLineByLine($hugeFile))
    // Find public static fields or methods.
    ->filter(static function (array $line) {return false !== strpos($line['content'], 'public static');})
    // Skip the first one.
    ->skip(1)
    // Limit the list to 3 results only.
    ->limit(3)
    // Add the line number at the end of each line.
    ->map(static function (array $line) {return sprintf('%s // Line %s', $line['content'], $line['number']);})
    // Implode into a string.
    ->implode(PHP_EOL);
```

## Advanced usage

You can choose to build your own collection object by extending the [Base](./src/Base.php) collection object or
by just creating a new [Operation](./src/Contract/Operation.php).

Each already existing operations of the [Collection](./src/Collection.php) class live in its own class file.

In order to extend the Collection features, create your own custom operation by creating an object implementing
the [Operation](./src/Contract/Operation.php) interface, then run it through the `Collection::run()` method.

```php
<?php

declare(strict_types=1);

include 'vendor/autoload.php';

use drupol\collection\Collection;
use drupol\collection\Contract\BaseCollection as CollectionInterface;
use drupol\collection\Operation\Operation;

$square = new class extends Operation {
    /**
     * {@inheritdoc}
     */
    public function on(iterable $collection): \Closure
    {
        return static function () use ($collection) {
                foreach ($collection as $item) {
                    yield $item ** 2;
                }
            };
    }
};

Collection::with(
  Collection::range(5, 15)
    ->run($square)
)->all();
```

Another way would be to create your own custom collection object:

In the following example and just for the sake of creating an example, the custom collection object will only be able to
transform any input (`iterable` or `\Generator`) into a regular array.

```php
<?php

declare(strict_types=1);

include 'vendor/autoload.php';

use drupol\collection\Base;
use drupol\collection\Contract\Allable;
use drupol\collection\Contract\Runable;
use drupol\collection\Operation\All;
use drupol\collection\Operation\Run;
use drupol\collection\Contract\Operation;

$customCollectionClass = new class extends Base implements Allable {

    /**
     * {@inheritdoc}
     */
    public function all(): array {
        return $this->run(new All());
    }
};

$customCollection = new $customCollectionClass(new ArrayObject(['A', 'B', 'C']));

print_r($customCollection->all()); // ['A', 'B', 'C']

$generator = function() {
  yield 'A';
  yield 'B';
  yield 'C';
};

$customCollection = new $customCollectionClass($generator);

print_r($customCollection->all()); // ['A', 'B', 'C']
```

The [Collection](./src/Collection.php) object implements all the interfaces from this library, and is set as `final`.
Use it like it is, decorated it or create your own object by using the same procedure as shown here.

## API

Most of the methods are [pure PHP functions](https://medium.com/better-programming/what-is-a-pure-function-3b4af9352f6f),
the methods always return the same values for the same inputs.

### Regular methods

| Methods       | Return type           | Source         |
| ------------- | --------------------- | -------------- |
| `all`         | array                 | [All.php](./src/Operation/All.php)
| `append`      | new Collection object | [Append.php](./src/Operation/Append.php)
| `apply`       | new Collection object | [Apply.php](./src/Operation/Apply.php)
| `chunk`       | new Collection object | [Chunk.php](./src/Operation/Chunk.php)
| `collapse`    | new Collection object | [Collapse.php](./src/Operation/Collapse.php)
| `combine`     | new Collection object | [Combine.php](./src/Operation/Combine.php)
| `count`       | int                   | [Count.php](./src/Operation/Count.php)
| `filter`      | new Collection object | [Filter.php](./src/Operation/Filter.php)
| `first`       | mixed                 | [First.php](./src/Operation/First.php)
| `flatten`     | new Collection object | [Flatten.php](./src/Operation/Flatten.php)
| `flip`        | new Collection object | [Flip.php](./src/Operation/Flip.php)
| `forget`      | new Collection object | [Forget.php](./src/Operation/Forget.php)
| `get`         | mixed                 | [Get.php](./src/Operation/Get.php)
| `getIterator` | Iterator              | [Collection.php](./src/Collection.php)
| `implode`     | string                | [Implode.php](./src/Operation/Implode.php)
| `intersperse` | new Collection object | [Intersperse.php](./src/Operation/Intersperse.php)
| `keys`        | new Collection object | [Keys.php](./src/Operation/Keys.php)
| `last`        | mixed                 | [Last.php](./src/Operation/Last.php)
| `limit`       | new Collection object | [Limit.php](./src/Operation/Limit.php)
| `map`         | new Collection object | [Collection.php](./src/Operation/Collection.php)
| `merge`       | new Collection object | [Merge.php](./src/Operation/Merge.php)
| `normalize`   | new Collection object | [Normalize.php](./src/Operation/Normalize.php)
| `nth`         | new Collection object | [Nth.php](./src/Operation/Nth.php)
| `only`        | new Collection object | [Only.php](./src/Operation/Only.php)
| `pad`         | new Collection object | [Pad.php](./src/Operation/Pad.php)
| `pluck`       | new Collection object | [Pluck.php](./src/Operation/Pluck.php)
| `prepend`     | new Collection object | [Prepend.php](./src/Operation/Prepend.php)
| `rebase`      | new Collection object | [Collection.php](./src/Operation/Collection.php)
| `reduce`      | mixed                 | [Reduce.php](./src/Operation/Reduce.php)
| `reduction`   | new Collection object | [Reduction.php](./src/Operation/Reduction.php)
| `run`         | mixed                 | [Run.php](./src/Operation/Run.php)
| `skip`        | new Collection object | [Skip.php](./src/Operation/Skip.php)
| `slice`       | new Collection object | [Slice.php](./src/Operation/Slice.php)
| `sort `       | new Collection object | [Sort.php](./src/Operation/Sort.php)
| `walk`        | new Collection object | [Walk.php](./src/Operation/Walk.php)
| `zip`         | new Collection object | [Zip.php](./src/Operation/Zip.php)

All those methods are described in [the Collection interface](./src/Contract/Collection.php), feel free to check it out for more information
about the kind of parameters they require.

### Static methods

| Methods       | Return type           | Source         |
| ------------- | --------------------- | -------------- |
| `empty`       | new Collection object | [Collection.php](./src/Collection.php)
| `iterate`     | new Collection object | [Collection.php](./src/Collection.php)
| `range`       | new Collection object | [Collection.php](./src/Collection.php)
| `times`       | new Collection object | [Collection.php](./src/Collection.php)
| `with`        | new Collection object | [Collection.php](./src/Collection.php)

All those methods are not described in [the Collection interface](./src/Contract/Collection.php),
but in the [Collection class](./src/Collection.php) itself, feel free to check it out to know more about the kind of parameters they require.

## Code style, code quality, tests and benchmarks

The code style is following [PSR-12](https://www.php-fig.org/psr/psr-12/) plus a set of custom rules, the package [drupol/php-conventions](https://github.com/drupol/php-conventions)
is responsible for this.

Every time changes are introduced into the library, [Travis CI](https://travis-ci.org/drupol/collection/builds) run the tests and the benchmarks.

The library has tests written with [PHPSpec](http://www.phpspec.net/).
Feel free to check them out in the `spec` directory. Run `composer phpspec` to trigger the tests.

[PHPInfection](https://github.com/infection/infection) is used to ensure that your code is properly tested, run `composer infection` to test your code.

## Contributing

See the file [CONTRIBUTING.md](.github/CONTRIBUTING.md) but feel free to contribute to this library by sending Github pull requests.
