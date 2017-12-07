<?php

namespace Bavix\Flow;

use Bavix\Exceptions\Invalid;
use Bavix\Exceptions\Runtime;
use Bavix\Flow\Directives\WithDirective;
use Bavix\Helpers\Arr;
use Bavix\Helpers\Str;
use Bavix\Lexer\Lexer;
use Bavix\FlowNative\FlowNative;
use Bavix\Lexer\Token;
use Bavix\Lexer\Validator;

class Flow
{

    const VER_TIME = 1512562842;
    const VERSION  = '1.0.0-alpha';

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
    protected $mapDirectives = [];

    /**
     * @var array
     */
    protected $functions = [
        'empty',
        'isset',
        'unset'
    ];

    /**
     * @var array
     */
    protected $raws;

    /**
     * @var FlowNative
     */
    protected $native;

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $tpl;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * Flow constructor.
     *
     * @param Native $native
     * @param array  $options
     */
    public function __construct(Native $native, array $options)
    {
        // configs
        $this->debug      = $options['debug'] ?? false;
        $this->mapDirectives = $options['directives'] ?? [];

        // init
        $this->native     = $native;
        $this->lexer      = new Lexer();
        $this->lexem      = new Lexem($this);
        $this->fileSystem = new FileSystem($this, $options['cache']);
        $this->native->setFlow($this);
    }

    public function debugMode()
    {
        return $this->debug;
    }

    public function fileSystem(): FileSystem
    {
        return $this->fileSystem;
    }

    public function ext(): string
    {
        return '.' . $this->ext;
    }

    /**
     * @return FlowNative
     */
    public function native(): FlowNative
    {
        return $this->native;
    }

    /**
     * @param array $tokens
     *
     * @return string
     */
    protected function fragment(array $tokens): string
    {
        $fragment = \implode(' ', Arr::map($tokens['tokens'] ?? $tokens, function (Token $token) {
            return $token->token;
        }));

        return \str_replace('. ', '.', $fragment);
    }

    public function build(array $data): string
    {
        $code     = [];
        $lastLast = null;
        $last     = null;

        /**
         * @var Token $token
         * @var Token $last
         * @var Token $lastLast
         */
        foreach ($data['tokens'] as $token)
        {
            $_token = clone $token;

            if ($_token->type === T_OBJECT_OPERATOR)
            {
                throw new Invalid('Undefined object operator `->`!');
            }

            if ($last && (!$lastLast ||
                    ($lastLast->type !== T_VARIABLE &&
                        $lastLast->type !== Validator::T_ENDBRACKET &&
                        $lastLast->type !== Validator::T_ENDARRAY))
                && $last->type === Validator::T_DOT)
            {
                $pop = Arr::pop($code);
                Arr::push($code, '\\' . WithDirective::class . '::last()');
                Arr::push($code, $pop);
            }

            if ($_token->type === Validator::T_CONCAT)
            {
                $_token->token = '.';
            }

            if ((!$last || ($last && $last->type !== Validator::T_DOT)) && $_token->type === T_FUNCTION)
            {
                if (!Arr::in($this->functions, $_token->token))
                {
                    $_token->token = '$this->' . $_token->token;
                }
            }

            if (Arr::in([Validator::T_BRACKET, T_ARRAY], $_token->type))
            {
                if ($last && $last->type === Validator::T_DOT)
                {
                    Arr::pop($code);
                }
            }

            if (Arr::in([T_VARIABLE, T_FUNCTION], $_token->type))
            {
                $_token->token = \str_replace('.', '->', $_token->token);

                if ($last && $last->type === Validator::T_DOT)
                {
                    Arr::pop($code);
                    Arr::push($code, '->');
                }

                if ($_token->type !== T_FUNCTION && (!$last || (
                            $last->type !== Validator::T_ENDARRAY &&
                            $last->type !== Validator::T_ENDBRACKET &&
                            $last->type !== Validator::T_DOT
                        )))
                {
                    $_token->token = '$' . $_token->token;
                }
            }

            $lastLast = $last;
            $last     = $_token;
            $code[]   = $_token->token;
        }

        return \implode($code);
    }

    public function path(string $view): string
    {
        if (!$this->fileSystem->has($view))
        {
            $this->fileSystem->set($view, $this->compile($view));
        }

        return $this->fileSystem->get($view);
    }

    protected function printers(array $raws, $escape = true)
    {
        $begin = $escape ? '\\htmlentities(' : '';
        $end   = $escape ? ')' : '';

        foreach ($raws as $raw)
        {
            $this->tpl = \preg_replace(
                '~' . \preg_quote($raw['code'], '~') . '~u',
                '<?php echo ' . $begin . $this->build($raw) . $end . '; ?>',
                $this->tpl,
                1
            );
        }
    }

    protected function directive(string $key, array $data, array $operator)
    {
        $class = __NAMESPACE__ . '\\Directives\\' . Str::ucFirst($key) . 'Directive';

        if (isset($this->mapDirectives[$key]))
        {
            $class = $this->mapDirectives[$key];
        }

        return new $class($this, $data, $operator);
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

            if (true !== $data && $this->lexem->closed($key))
            {
                $dir = $this->popDirective($key);

                $this->tpl = \preg_replace(
                    '~' . \preg_quote($operator['code'], '~') . '~u',
                    $dir->endDirective(),
                    $this->tpl,
                    1
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
     * @param array  $data
     *
     * @return string
     */
    public function render(string $view, array $data = []): string
    {
        return $this->native()->render(
            $this->path($view),
            $data
        );
    }

    /**
     * @param string $view
     *
     * @return string
     */
    public function compile(string $view): string
    {
        $path      = $this->native->path($view . $this->ext());
        $this->tpl = \file_get_contents($path);
        $tokens    = $this->lexer->tokens($this->tpl);

        $this->literals  = $tokens[Lexer::LITERAL];
        $this->printers  = $tokens[Lexer::PRINTER];
        $this->operators = $tokens[Lexer::OPERATOR];
        $this->raws      = $tokens[Lexer::RAW];

        $this->printers($this->printers);
        $this->printers($this->raws, false);
        $this->operators();

        // check directives
        foreach ($this->directives as $name => $items)
        {
            if ($this->lexem->closed($name))
            {
                if (!empty($items))
                {
                    throw new Runtime(
                        \sprintf(
                            'Directive %s not closed',
                            get_class(Arr::pop($items))
                        )
                    );
                }
            }
        }

        foreach ($this->literals as $key => $literal)
        {
            $this->tpl = \str_replace($key, $literal, $this->tpl);
        }

        return $this->tpl;
    }

}
