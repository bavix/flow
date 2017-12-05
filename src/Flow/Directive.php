<?php

namespace Bavix\Flow;

use Bavix\Helpers\Str;

abstract class Directive
{

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $operator;

    /**
     * Directive constructor.
     *
     * @param array $data
     * @param array $operator
     */
    public function __construct(array $data, array $operator)
    {
        $this->data     = $data;
        $this->operator = $operator;
    }

    protected function randVariable(): string
    {
        return '$' . Str::random(12);
    }

    /**
     * @return string
     */
    public function endDirective(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string
     */
    abstract public function render(): string;

}
