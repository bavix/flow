<?php

namespace Bavix\Flow;

use Bavix\Exceptions\Invalid;

/**
 * Class Loop
 *
 * @package Bavix\Flow
 *
 * @property-read $first
 * @property-read $firstIndex
 * @property-read $index
 * @property-read $iteration
 * @property-read $key
 * @property-read $last
 * @property-read $lastIndex
 */
class Loop
{

    protected $iteration = 0;
    protected $index     = -1;
    protected $key;

    protected $first;
    protected $firstIndex;
    protected $last;
    protected $lastIndex;

    public function __construct($data)
    {
        if (!empty($data))
        {
            $this->firstIndex = \key($data);
            $this->key        = $this->firstIndex;
            \end($data);

            $this->lastIndex = \key($data);
            \reset($data);
        }
    }

    public function next($key)
    {
        $this->index++;
        $this->iteration++;
        $this->key   = $key;
        $this->first = $this->firstIndex === $this->key;
        $this->last  = $this->lastIndex === $this->key;
    }

    public function __get(string $name)
    {
        return $this->{$name};
    }

    public function __set(string $name, $value)
    {
        throw new Invalid('Loop::' . $name . ' readonly');
    }

    public function __isset(string $name)
    {
        return \property_exists($this, $name);
    }

}
