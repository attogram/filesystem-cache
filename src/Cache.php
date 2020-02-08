<?php
/**
 * filesystem-cache
 *
 * @author Attogram Project <https://github.com/attogram>
 * @license MIT
 * @see <https://github.com/attogram/filesystem-cache>
 */
declare(strict_types = 1);

namespace Attogram\Filesystem;

use function array_pop;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function htmlentities;
use function is_dir;
use function is_readable;
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
    const VERSION = '0.1.1';

    /**
     * @var bool $verbose
     */
    public $verbose = false;

    /**
     * @var string $cacheDirectory - cache directory, with trailing slash
     */
    private $cacheDirectory = 'cache' . DIRECTORY_SEPARATOR;

    /**
     * @var string $filename - active filename, full path
     */
    private $filename;

    /**
     * @param string $cacheDirectory (optional, default '')
     * @param bool $verbose (optional, default false)
     * @return void
     */
    public function __construct(string $cacheDirectory = '', $verbose = false)
    {
        if ($cacheDirectory) {
            // @TODO validate cacheDirectory format and existence
            $this->cacheDirectory = $cacheDirectory;
        }
        if ($verbose) {
            $this->verbose = true;
        }
    }

    /**
     * Does a cached file exist for this key?
     * - also sets $this->filename via setFilename()
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        if ($this->setFilename($key)
            && file_exists($this->filename)
            && is_readable($this->filename)
        ) {
            return true;
        }
        $this->verbose('exists: File Does Not Exist, or Not Readable: key=' . $key);
    
        return false;
    }

    /**
     * @param string $key
     * @return mixed - contents of cached file, or false on error
     */
    public function get(string $key)
    {
        if (!$this->exists($key)) {
            return false;
        }
        $contents = @file_get_contents($this->filename);
        if (empty($contents)) {
            $this->error("get: NO CONTENTS: $key - " . $this->filename);

            return false;
        }
        $this->verbose("get: key:$key - strlen.contents:" . strlen($contents) . " - file:" . $this->filename);

        return $contents;
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function set(string $key, string $value): bool
    {
        $this->setFilename($key);
        $parts = explode(DIRECTORY_SEPARATOR, $this->filename);
        array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            $dir .= $part . DIRECTORY_SEPARATOR;
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }
        $bytes = file_put_contents($this->filename, $value);
        if (false === $bytes) {
            $this->error('set: FAILED write path: ' . $this->filename);

            return false;
        }
        $this->verbose("set: key:$key - file:" . $this->filename . " - bytes:$bytes");

        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        if (!$this->exists($key)) {
            return false;
        }
        if (!unlink($this->filename)) {
            $this->error('delete: unlink failed: ' . $key);
    
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @return int - unix time stamp, or 0 on error
     */
    public function age(string $key): int
    {
        if (!$this->exists($key)) {
            return 0;
        }
        $age = filemtime($this->filename);
        if (!is_int($age)) {
            $age = 0;
        }

        return $age;
    }

    /**
     * Set the filename from a key string
     *
     * @param string $key
     * @return bool
     */
    private function setFilename(string $key): bool
    {
        $this->filename = '';
        $md5 = md5($key);
        if (empty($md5)) {
            $this->error('getFilename: md5 failed: ' . $key);

            return false;
        }
        $firstDirectory = substr($md5, 0, 1); // get first character
        if (strlen($firstDirectory) !== 1) {
            $this->error('getFilename: 1st extract failed: ' . $key);
    
            return false;
        }
        $secondDirectory = substr($md5, 1, 2); // get second and third character
        if (strlen($secondDirectory) !== 2) {
            $this->error('getFilename: 2nd extract failed: ' . $key);

            return false;
        }
        $this->filename = $this->cacheDirectory . $firstDirectory . DIRECTORY_SEPARATOR
            . $secondDirectory . DIRECTORY_SEPARATOR . $md5 . '.cache';

        return true;
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
