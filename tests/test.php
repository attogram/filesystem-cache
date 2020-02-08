<?php

require_once('../src/Cache.php');

use Attogram\Filesystem\Cache;

$cacheDirectory = 'cache' . DIRECTORY_SEPARATOR;
$verbose = false;

$cache = new Cache($cacheDirectory, $verbose);

var_dump($cache);

$key = 'test';
$value = 'foobar';

print "-- exists($key) = "; var_dump($cache->exists($key));
print "-- age($key) = "; var_dump($cache->age($key));
print "-- get($key) = "; var_dump($cache->get($key));

print "- set($key, $value) = "; var_dump($cache->set($key, $value));

print "-- exists($key) = "; var_dump($cache->exists($key));
print "-- age($key) = "; var_dump($cache->age($key));
print "-- get($key) = "; var_dump($cache->get($key));

print "- delete($key) = "; var_dump($cache->delete($key));

print "-- exists($key) = "; var_dump($cache->exists($key));
print "-- age($key) = "; var_dump($cache->age($key));
print "-- get($key) = "; var_dump($cache->get($key));
