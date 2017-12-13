<?php

namespace Bavix\Flow\Minify;

class HTML
{

    /**
     * @var string
     */
    protected $buffer;

    /**
     * @var string[]
     */
    protected $extends;

    /**
     * HTML constructor.
     *
     * @param string $buffer
     * @param array $extends
     */
    public function __construct(string $buffer, array $extends)
    {
        $this->buffer  = $buffer;
        $this->extends = $extends;
    }

    /**
     * @return string
     */
    public function apply(): string
    {
        foreach ($this->extends as $extend)
        {
            /**
             * @var Extension $object
             */
            $object = new $extend($this->buffer);
            $object->apply();

            $this->buffer = (string)$object;
        }

        return $this->buffer;
    }

}
