<?php

namespace Bavix\Flow;

use Bavix\FlowNative\FlowNative;

class Flow
{

    /**
     * @var string
     */
    protected $ext = '.flow';

    /**
     * @var Lexer
     */
    protected $lexer;

    /**
     * @var FlowNative
     */
    protected $native;

    /**
     * Flow constructor.
     *
     * @param FlowNative $native
     */
    public function __construct(FlowNative $native)
    {
        $this->native = $native;
        $this->lexer = new Lexer();
    }

    /**
     * @param string $view
     */
    public function compile($view)
    {
        $path = $this->native->path($view . '.' . $this->ext);
        // todo
    }

}
