<?php

namespace Bavix\Flow;

use Bavix\Exceptions\Invalid;

class Token
{

    /**
     * @var array
     */
    protected $data = [
        'type'  => null,
        'token' => null,
        'name'  => null
    ];

    /**
     * Token constructor.
     *
     * @param string $data
     * @param string $type
     */
    public function __construct($data, $type)
    {
        $this->data['token'] = $data;
        $this->data['type']  = $type;
        $this->data['name']  = \token_name($type);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->data['token'];
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        if ($name === 'type')
        {
            $this->data['name'] = \token_name($value);
        }

        if ($name === 'name')
        {
            throw new Invalid('Undefined index `name`');
        }

        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return $this->data;
    }

}
