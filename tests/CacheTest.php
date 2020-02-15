<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Attogram\Filesystem\Cache;

final class CacheTest extends TestCase
{
    /**
     * @var Attogram\Filesystem\Cache
     */
    protected $cache;

    protected function setUp(): void
    {
        $this->cache = new Cache();
    }

    public function testClass()
    {
        $this->assertInstanceOf(Cache::class, $this->cache);
    }

    public function testNonExistant()
    {
        $key = 'key-1';
        $this->assertFalse($this->cache->exists($key));
        $this->assertSame(0, $this->cache->age($key));
        $this->assertFalse($this->cache->get($key));
        $this->assertFalse($this->cache->delete($key));
    }

    public function testSet()
    {
        $key = 'key-2';
        $value = 'foobar';
        $this->assertTrue($this->cache->set($key, $value));
        $this->assertTrue($this->cache->exists($key));
        if (method_exists($this, 'assertIsInt')) { // added in PHPUnit 8
            $this->assertIsInt($this->cache->age($key));
        }
        if (method_exists($this, 'assertInternalType')) { // deprecated in PHPUnit 8, removed in PHPUnit 9
            $this->assertInternalType('int', $this->cache->age($key)); 
        }
        $this->assertSame($value, $this->cache->get($key));
    }

    public function testDelete()
    {
        $key = 'key-2';
        $this->assertTrue($this->cache->exists($key));
        $this->assertTrue($this->cache->delete($key));
        $this->assertFalse($this->cache->exists($key));
        $this->assertSame(0, $this->cache->age($key));
        $this->assertFalse($this->cache->get($key));
    }
}
