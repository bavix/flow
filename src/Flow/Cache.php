<?php

namespace Bavix\Flow;

use Psr\Cache\CacheItemPoolInterface;

class Cache
{

    /**
     * @var CacheItemPoolInterface
     */
    protected static $pool;

    /**
     * @var array
     */
    protected static $cache = [];

    /**
     * @var array
     */
    protected static $names = [];

    /**
     * @return CacheItemPoolInterface
     */
    protected static function getPool()
    {
        return static::$pool;
    }

    /**
     * @param CacheItemPoolInterface $pool
     */
    public static function setPool($pool)
    {
        static::$pool = $pool;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected static function name(string $key): string
    {
        if (!isset(static::$names[$key]))
        {
            static::$names[$key] = Flow::VERSION . \sha1($key);
        }

        return static::$names[$key];
    }

    /**
     * @param string $key
     *
     * @return null|\Psr\Cache\CacheItemInterface
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function getItem(string $key)
    {
        if (static::getPool())
        {
            return static::$pool->getItem(static::name($key));
        }

        return null;
    }

    /**
     * @param string   $key
     * @param callable $callback
     *
     * @return mixed
     */
    protected static function syncCache(string $key, callable $callback)
    {
        if (!isset(static::$cache[$key]))
        {
            static::$cache[static::name($key)] = $callback();
        }

        return static::$cache[static::name($key)];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected static function getCache(string $key)
    {
        return static::$cache[static::name($key)] ?? null;
    }

    /**
     * @param string   $key
     * @param callable $callback
     *
     * @return mixed
     */
    public static function get(string $key, callable $callback)
    {
        $item = static::getItem($key);

        if (!$item)
        {
            return static::syncCache($key, $callback);
        }

        if (!$item->isHit())
        {
            $item->set(
                static::syncCache($key, $callback)
            );

            static::getPool()->save($item);
        }

        $cache = static::getCache($key);

        if ($cache)
        {
            return $cache;
        }

        return $item->get();
    }

}
