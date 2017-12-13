<?php

namespace Bavix\Flow;

class Property
{

    protected static $path;
    protected static $data = [];

    protected static function path(): string
    {
        if (!static::$path)
        {
            static::$path = \dirname(__DIR__, 2) . '/config/';
        }

        return static::$path;
    }

    public static function get($name)
    {
        if (empty(static::$data[$name]))
        {
            static::$data[$name] = require static::path() . $name . '.php';
        }

        return static::$data[$name];
    }

}
