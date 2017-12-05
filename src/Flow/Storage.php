<?php

namespace Bavix\Flow;

use Bavix\Helpers\Arr;

class Storage implements \ArrayAccess
{

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Storage constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    protected function explode(string $key): array
    {
        return \explode('.', $key);
    }

    /**
     * @param array|object $object
     * @param string       $property
     *
     * @return mixed
     */
    protected function &read(&$object, $property)
    {
        if (\is_array($object) || $object instanceof \ArrayAccess)
        {
            return $object[$property];
        }

        return $object->$property;
    }

    /**
     * @param array $keys
     *
     * @return mixed
     */
    protected function &offset(array $keys)
    {
        $row = &$this->read($this->data, Arr::shift($keys));

        foreach ($keys as $key)
        {
            $row = &$this->read($row, $key);
        }

        return $row;
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->offset($this->explode($offset));
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $row = &$this->offset($this->explode($offset));
        $row = $value;
    }

    /**
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $explode = $this->explode($offset);
        $last    = Arr::pop($explode);
        $rows    = &$this->data;

        if (!empty($explode))
        {
            $rows = &$this->offset($explode);
        }

        if (\is_array($rows) || $rows instanceof \ArrayAccess)
        {
            unset($rows[$last]);

            return;
        }

        unset($rows->$last);
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->offsetGet($offset) !== null;
    }

}
