<?php
/**
 * filesystem-cache - https://github.com/attogram/filesystem-cache
 */
declare(strict_types = 1);

namespace Attogram\Filesystem;

use function array_pop;
use function count;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function htmlentities;
use function is_array;
use function is_dir;
use function is_readable;
use function json_decode;
use function md5;
use function mkdir;
use function print_r;
use function strlen;
use function substr;
use function unlink;

class Cache
{
    /**
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * @var bool $verbose
     */
    public $verbose = false;

    /**
     * @var string $cacheDirectory - cache directory, with trailing slash
     */
    private $cacheDirectory = '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;

    /**
     * @param string $cacheDirectory (optional, default '')
     * @return void
     */
    public function __construct(string $cacheDirectory = '')
    {
        if ($cacheDirectory) {
            $this->cacheDirectory = $cacheDirectory;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        $file = $this->getFilename($key);
        if (empty($file)) {
            $this->error('exists: EMPTY PATH: ' . $key);

            return false;
        }
        if (!file_exists($file)) {
            return false;
        }
        if (!is_readable($file)) {
            $this->error('exists: NOT READABLE: ' . $file);

            return false;
        }
        return true;
    }

    /**
     * @param string $key
     * @return array|false
     */
    public function get(string $key)
    {
        if (!$this->exists($key)) {
            $this->verbose("get: NOT FOUND: $key");

            return false;
        }
        $file = $this->getFilename($key);
        if (empty($file)) {
            $this->error("get: EMPTY PATH: $key - $file");

            return false;
        }
        $contents = @file_get_contents($file);
        if (empty($contents)) {
            $this->error("get: NO CONTENTS: $key - $file");

            return false;
        }

        return $contents;

        //$data = @json_decode($contents, true);
        //if (!is_array($data)) {
        //    $this->error("get: JSON DECODE FAILED: $key - $file");
        //
        //    return false;
        //}
        //$this->verbose("get: $key - " . count($data) . " - $file");
        //
        //return $data;
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function set(string $key, string $value): bool
    {
        $file = $this->getFilename($key);
        $parts = explode(DIRECTORY_SEPARATOR, $file);
        array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            $dir .= $part . DIRECTORY_SEPARATOR;
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }
        $bytes = file_put_contents($file, $value);
        if (false === $bytes) {
            $this->error('set: FAILED write path: ' . $file);

            return false;
        }
        $this->verbose("set: $key - $file - $bytes");

        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        if (!$this->exists($key)) {
            $this->error('delete: does not exist: ' . $key);

            return false;
        }
        $file = $this->getFilename($key);
        if (!$file) {
            $this->error('delete: path empty: ' . $key);

            return false;
        }
        if (unlink($file)) {
            return true;
        }
        $this->error('delete: unlink failed');
    
        return false;
    }

    /**
     * @param string $key
     * @return int|false - unix time stamp, or false on error
     */
    public function age(string $key)
    {
        if (!$this->exists($key)) {
            $this->verbose('age: NOT FOUND: ' . $key);

            return false;
        }
        $file = $this->getFilename($key);
        if (!$file) {
            $this->error('age: NO PATH: ' . $file);

            return false;
        }

        return filemtime($file);
    }

    /**
     * @param string $key
     * @return string - full path to cache file, or empty string
     */
    private function getFilename(string $key): string
    {
        $md5 = md5($key);
        if (empty($md5)) {
            $this->error('getFilename: md5 failed: ' . $key);

            return '';
        }
        $first = substr($md5, 0, 1);
        if (!strlen($first)) {
            $this->error('getFilename: 1st extract failed: ' . $key);

            return '';
        }
        $second = substr($md5, 1, 2);
        if (!strlen($second) == 2) {
            $this->error('getFilename: 2nd extract failed: ' . $key);

            return '';
        }
    
        return $this->cacheDirectory . $first . DIRECTORY_SEPARATOR . $second . DIRECTORY_SEPARATOR . $md5 . '.gz';
    }

    /**
     * @param mixed $message
     * @return void
     */
    private function verbose($message = '')
    {
        if ($this->verbose) {
            print gmdate('Y-m-d H:i:s') . ': ' . htmlentities(print_r($message, true)) . "\n";
        }
    }

    /**
     * @param mixed $message
     * @return void
     */
    private function error($message = '')
    {
        print gmdate('Y-m-d H:i:s') . ': ERROR: ' . htmlentities(print_r($message, true)) . "\n";
    }
}
