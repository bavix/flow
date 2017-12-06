<?php

namespace Bavix\Flow;

use Bavix\Exceptions\Invalid;
use Bavix\Helpers\Str;

abstract class Directive
{

    /**
     * @var Flow
     */
    protected $flow;

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
     * @param Flow  $flow
     * @param array $data
     * @param array $operator
     */
    public function __construct(Flow $flow, array $data, array $operator)
    {
        $this->flow     = $flow;
        $this->data     = $data;
        $this->operator = $operator;
    }

    protected function randVariable(): string
    {
        return '$_' . Str::random(16);
    }

    /**
     * @return string
     */
    public function endDirective(): string
    {
        throw new Invalid('Undefined directive `' . $this->operator['fragment']);
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
