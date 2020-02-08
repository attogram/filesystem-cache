<?php

require_once('../src/Cache.php');

use Attogram\Filesystem\Cache;

$cacheDirectory = 'cache' . DIRECTORY_SEPARATOR;
$verbose = false;
$key = 'test';
$value = 'foobar';
//$value = json_encode(['foo' => 'bar']);


$cache = new Cache($cacheDirectory, $verbose);

print "Test: " . get_class($cache) . ' v' . $cache::VERSION . "\n";

//var_dump($cache);
printStats($key);

print "cache->set($key, $value) = ";
var_dump($cache->set($key, $value));
printStats($key);

print "cache->delete($key) = ";
var_dump($cache->delete($key));
printStats($key);


function printStats($key)
{
    global $cache;
    print "-- cache->exists($key) = ";
    var_dump($cache->exists($key));
    print "-- cache->age($key) = ";
    var_dump($cache->age($key));
    print "-- cache->get($key) = ";
    var_dump($cache->get($key));
}