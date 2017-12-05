<?php

namespace Bavix\Flow;

use Bavix\Exceptions\Invalid;
use Bavix\FlowNative\Helper;
use Bavix\Helpers\Arr;
use Bavix\Helpers\Str;
use Bavix\Lexer\Lexer;
use Bavix\FlowNative\FlowNative;
use Bavix\Lexer\Token;
use Bavix\Lexer\Validator;

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
    protected $directives = [];

    /**
     * @var array
     */
    protected $raws;

    /**
     * @var FlowNative
     */
    protected $native;

    /**
     * @var string
     */
    protected $tpl;

    /**
     * Flow constructor.
     *
     * @param FlowNative $native
     */
    public function __construct(FlowNative $native)
    {
        $this->native = $native;
        $this->lexer  = new Lexer();
        $this->lexem  = new Lexem($this);
    }

    /**
     * @param array $tokens
     *
     * @return string
     */
    protected function fragment(array $tokens)
    {
        return \implode(' ', Arr::map($tokens['tokens'] ?? $tokens, function (Token $token) {
            return $token->token;
        }));
    }

    public function build(array $data)
    {
        $code = [];
        $last = null;

        /**
         * @var Token $token
         * @var Token $last
         */
        foreach ($data['tokens'] as $token)
        {
            $_token = clone $token;

            if ($_token->type === T_OBJECT_OPERATOR)
            {
                throw new Invalid('Undefined object operator `->`!');
            }

            if ($_token->type === Validator::T_CONCAT)
            {
                $_token->token = '.';
            }

            if ($_token->type === T_FUNCTION)
            {
                $_token->token = '$this->' . $_token->token;
            }

            if ($_token->type === T_VARIABLE)
            {
                $_token->token = \str_replace('.', '->', $_token->token);

                if ($last && $last->type === Validator::T_DOT)
                {
                    Arr::pop($code);
                    Arr::push($code, '->');
                }

                if (!$last || (
                        $last->type !== Validator::T_ENDARRAY &&
                        $last->type !== Validator::T_ENDBRACKET &&
                        $last->type !== Validator::T_DOT
                    ))
                {
                    $_token->token = '$' . $_token->token;
                }
            }

            $last   = $_token;
            $code[] = $_token->token;
        }

        return \implode($code);
    }

    protected function printers($raws, $escape = true)
    {
        $begin = $escape ? '\\htmlentities(' : '';
        $end   = $escape ? ')' : '';

        foreach ($raws as $code => $raw)
        {
            $this->tpl = str_replace(
                $code,
                '<?php echo ' . $begin . $this->build($raw) . $end . '; ?>',
                $this->tpl
            );
        }
    }

    protected function directive(string $key, array $data, array $operator)
    {
        $class = __NAMESPACE__ . '\\Directives\\' . Str::ucFirst($key) . 'Directive';

        return new $class($data, $operator);
    }

    /**
     * @param string    $key
     * @param Directive $directive
     */
    protected function pushDirective(string $key, Directive $directive)
    {
        if (empty($this->directives[$key]))
        {
            $this->directives[$key] = [];
        }

        $this->directives[$key][] = $directive;
    }

    /**
     * @param string $key
     *
     * @return Directive
     */
    protected function popDirective(string $key): Directive
    {
        return Arr::pop($this->directives[$key]);
    }

    /**
     * @param array  $operator
     * @param string $key
     *
     * @return bool
     */
    protected function ifEnd($operator, string $key): bool
    {
        if (0 === Str::pos($key, 'end'))
        {
            $key  = Str::sub($key, 3);
            $data = $this->lexem->data($key);

            if (true !== $data)
            {
                $dir = $this->popDirective($key);

                $this->tpl = \str_replace(
                    $operator['code'],
                    $dir->endDirective(),
                    $this->tpl
                );
            }

            return !$data;
        }

        return false;
    }

    protected function operators()
    {
        foreach ($this->operators as $operator)
        {
            /**
             * @var Token $_token
             */
            $_token = current($operator['tokens']);
            $data   = $this->lexem->data($_token->token);

            $end = !$this->ifEnd($operator, $_token->token);

            if ($end && true !== $data)
            {
                $data = $this->lexem->apply(
                    $_token->token,
                    $this->fragment($operator)
                );

                /**
                 * @var Directive $directive
                 */
                $directive = $this->directive($_token->token, $data ?: [], $operator);
                $this->pushDirective($_token->token, $directive);

                $this->tpl = \str_replace(
                    $operator['code'],
                    $directive->render(),
                    $this->tpl
                );
            }
        }
    }

    /**
     * @param string $view
     *
     * @return string
     */
    public function compile($view)
    {
        $path      = $this->native->path($view . '.' . $this->ext);
        $this->tpl = file_get_contents($path);
        $tokens    = $this->lexer->tokens($this->tpl);

        $this->literals  = $tokens[Lexer::LITERAL];
        $this->printers  = $tokens[Lexer::PRINTER];
        $this->operators = $tokens[Lexer::OPERATOR];
        $this->raws      = $tokens[Lexer::RAW];

        $this->printers($this->printers, false);
        $this->printers($this->raws);
        $this->operators();

        foreach ($this->literals as $key => $literal)
        {
            $this->tpl = \str_replace($key, $literal, $this->tpl);
        }

        return $this->tpl;
    }

}
