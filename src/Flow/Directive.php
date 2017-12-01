<?php

namespace Flow;

use Bavix\Lexer\Token;

abstract class Directive
{

    /**
     * @var array
     */
    protected $data;

    /**
     * Directive constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function variable(Token $token)
    {

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
