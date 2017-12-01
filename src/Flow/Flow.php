<?php

namespace Bavix\Flow;

use Bavix\Helpers\Arr;
use Bavix\Lexer\Lexer;
use Bavix\FlowNative\FlowNative;
use Bavix\Lexer\Token;

class Flow
{

    /**
     * @var string
     */
    protected $ext = 'bxf';

    /**
     * @var Lexer
     */
    protected $lexer;

    /**
     * @var Lexem
     */
    protected $lexem;

    /**
     * @var array
     */
    protected $literals;

    /**
     * @var array
     */
    protected $printers;

    /**
     * @var array
     */
    protected $operators;

    /**
     * @var array
     */
    protected $raws;

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
        $this->lexer  = new Lexer();
        $this->lexem  = new Lexem();
    }

    protected function fragment(array $tokens)
    {
        return implode(' ', Arr::map($tokens, function (Token $token) {
            return $token->token;
        }));
    }

    /**
     * @param string $view
     *
     * @return string
     */
    public function compile($view)
    {
        $path   = $this->native->path($view . '.' . $this->ext);
        $tpl    = file_get_contents($path);
        $tokens = $this->lexer->tokens($tpl);

        $this->literals  = $tokens[Lexer::LITERAL];
        $this->printers  = $tokens[Lexer::PRINTER];
        $this->operators = $tokens[Lexer::OPERATOR];
        $this->raws      = $tokens[Lexer::RAW];

        foreach ($this->operators as $operator)
        {
            /**
             * @var $_tokens Token[]
             */
            $_tokens = $operator['tokens'];

            // fragment for regExp
            $fragment = $this->fragment($_tokens);

            /**
             * @var $_operator Token
             */
            $_operator = array_shift($_tokens);
            $lexemes = $this->lexem->data($_operator->token);

            if (true !== $lexemes)
            {
                foreach ($lexemes as $lexeme)
                {
                    if (preg_match($lexeme['regexp'], $fragment))
                    {
                        var_dump($operator);
                        break;
                    }
                }
            }

//            $this->lexem->data($operator)
//            var_dump($operator);
        }

        die;

        // todo
        return $this->lexer->tokens($tpl);
    }

}
