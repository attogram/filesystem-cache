<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Attogram\Filesystem\Cache;

final class CacheTest extends TestCase
{
    public function getCache(): Cache
    {
        return new Cache();
    }

    public function testClass(): void
    {
        $this->assertInstanceOf(Cache::class, $this->getCache());
    }
}
