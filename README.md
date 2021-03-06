# filesystem-cache

Filesystem-based cache system for PHP 7.

* Repository: <https://github.com/attogram/filesystem-cache>
* Packagist: <https://packagist.org/packages/attogram/filesystem-cache>
* CodeClimate: [![Maintainability](https://api.codeclimate.com/v1/badges/74acc2c81db24cc8fb75/maintainability)](https://codeclimate.com/github/attogram/filesystem-cache/maintainability)
* Travis-CI: [![Build Status](https://travis-ci.org/attogram/filesystem-cache.svg?branch=master)](https://travis-ci.org/attogram/filesystem-cache)

## Usage

```php
use Attogram\Filesystem\Cache;

$cacheDirectory = '../cache/'; // must include trailing slash

$cache = new Cache($cacheDirectory);
```

## Functions

* public function exists(string $key): bool
* public function get(string $key)
* public function set(string $key, string $value): bool
* public function delete(string $key): bool
* public function age(string $key): int

## Similar projects

* <https://github.com/Gregwar/Cache>
* <https://github.com/cosenary/Simple-PHP-Cache>
* <https://github.com/jdorn/FileSystemCache>
* <https://github.com/php-cache/filesystem-adapter>
* <https://github.com/Wruczek/PHP-File-Cache>
* <https://github.com/saltybeagle/StaticCache>
* <https://github.com/sarahman/simple-filesystem-cache>
* <https://github.com/chrisullyott/simple-cache>
* <https://github.com/override2k/psr-cache>
* ...
