<?php

namespace Bavix\Flow;

use Bavix\Exceptions\Invalid;
use Bavix\Exceptions\Runtime;
use Bavix\Flow\Directives\WithDirective;
use Bavix\FlowNative\FlowNative;
use Bavix\Helpers\Arr;
use Bavix\Helpers\JSON;
use Bavix\Helpers\Str;
use Bavix\Lexer\Lexer;
use Bavix\Lexer\Token;
use Bavix\Lexer\Validator;
use JSMin\JSMin;
use Psr\Cache\CacheItemPoolInterface;

class Flow
{

    const VER_TIME = 1512748526;
    const VERSION  = '1.0.0-alpha3';

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var CacheItemPoolInterface
     */
    protected $pool;

    /**
     * @var string
     */
    protected $ext = 'bxf';

    /**
     * @var Lexer
     */
    protected $lexer;

    /**
     * @var Lexeme
     */
    protected $lexeme;

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
     * @todo подумать над списком функций
     *
     * @var array
     */
    protected $functions = [
        'empty',
        'isset',
        'unset',

        'compact',
        'extract',
    ];

    /**
     * @var array
     */
    protected $lexemes = [];

    /**
     * @var array
     */
    protected $rows;

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
     * @var bool
     */
    protected $minify;

    /**
     * Flow constructor.
     *
     * @param Native $native
     * @param array  $options
     */
    public function __construct(Native $native, array $options)
    {
        // configs
        $this->mapDirectives = $options['directives'] ?? [];
        $this->lexemes       = $options['lexemes'] ?? [];
        $this->minify        = $options['minify'] ?? false;
        $this->debug         = $options['debug'] ?? false;
        $this->pool          = $options['cache'] ?? null;

        // init
        $this->native     = $native;
        $this->fileSystem = new FileSystem($this, $options['compile']);
        $this->native->setFlow($this);
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function pool()
    {
        return $this->pool;
    }

    protected function loadLexemes(): self
    {
        foreach ($this->lexemes as $folder)
        {
            $this->lexeme->addFolder($folder);
        }

        return $this;
    }

    /**
     * @param Lexeme $lexeme
     *
     * @return $this
     */
    public function setLexeme(Lexeme $lexeme): self
    {
        $this->lexeme = $lexeme;

        return $this->loadLexemes();
    }

    /**
     * @param Lexer $lexer
     *
     * @return $this
     */
    public function setLexer(Lexer $lexer): self
    {
        $this->lexer = $lexer;

        return $this;
    }

    /**
     * @return Lexeme
     */
    public function lexeme(): Lexeme
    {
        if (!$this->lexeme)
        {
            $this->setLexeme(new Lexeme($this));
        }

        return $this->lexeme;
    }

    /**
     * @return Lexer
     */
    public function lexer(): Lexer
    {
        if (!$this->lexer)
        {
            $this->lexer = new Lexer();
        }

        return $this->lexer;
    }

    /**
     * @return bool
     */
    public function debugMode(): bool
    {
        return $this->debug;
    }

    /**
     * @return FileSystem
     */
    public function fileSystem(): FileSystem
    {
        return $this->fileSystem;
    }

    /**
     * @return string
     */
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
        $data = Arr::map($tokens['tokens'] ?? $tokens, function (Token $token) {
            return $token->token;
        });

        return \str_replace(
            '. ',
            '.',
            \implode(' ', $data)
        );
    }

    protected function store(string $key, $data)
    {
        if ($pool = $this->pool())
        {
            $name = $this->storeName($key);
            $item = $pool->getItem($name);
            $item->set($data);
            $this->cache[$name] = $data;
            $pool->save($item);
        }

        return $data;
    }

    protected function storeName(string $key)
    {
        return $key . self::VERSION;
    }

    protected function storeItem(string $name)
    {
        $pool = $this->pool();

        if ($pool && empty($this->cache[$name]))
        {
            $item = $pool->getItem($name);

            if ($item->isHit())
            {
                $this->cache[$name] = $item->get();
            }
        }

        return $this->cache[$name] ?? null;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function build(array $data): string
    {
        $_storeKey = JSON::encode($data);
        $storeItem = $this->storeItem($_storeKey);

        if ($storeItem)
        {
            return $storeItem;
        }

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

            if (Arr::in([T_NEW, T_CLONE, T_INSTEADOF, T_INSTANCEOF, T_AS], $_token->type))
            {
                $lastLast = $last;
                $last     = $_token;

                if (!Arr::in([T_NEW, T_CLASS], $_token->type))
                {
                    $code[]   = ' ';
                }

                $code[]   = $_token->token;
                $code[]   = ' ';
                continue;
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
                if (Str::ucFirst($_token->token) !== $_token->token &&
                    !Arr::in($this->functions, $_token->token))
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

                if (Str::ucFirst($_token->token) === $_token->token)
                {
                    $_token->type = T_CLASS;
                }

                if (!Arr::in([T_FUNCTION, T_CLASS], $_token->type) &&
                    (!$last || !Arr::in([
                            Validator::T_ENDBRACKET,
                            Validator::T_ENDARRAY,
                            Validator::T_DOT,
                            T_NS_SEPARATOR
                        ], $last->type)))
                {
                    $_token->token = '$this->' . $_token->token;
                }
            }

            $lastLast = $last;
            $last     = $_token;
            $code[]   = $_token->token;
        }

        return $this->store($_storeKey, \implode($code));
    }

    /**
     * @param string $view
     *
     * @return string
     */
    protected function minify(string $view): string
    {
        $html = $this->compile($view);

        if ($this->minify)
        {
            $html = \Minify_HTML::minify($html, [
                'cssMinifier' => [\Minify_CSSmin::class, 'minify'],
                'jsMinifier'  => [JSMin::class, 'minify'],
            ]);
        }

        return $html;
    }

    /**
     * @param string $view
     *
     * @return string
     */
    public function path(string $view): string
    {
        if (!$this->fileSystem->has($view))
        {
            $this->fileSystem->set($view, $this->minify($view));
        }

        return $this->fileSystem->get($view);
    }

    /**
     * @param array $rows
     * @param bool  $escape
     */
    protected function printers(array $rows, $escape = true)
    {
        $begin = $escape ? '\\htmlspecialchars(' : '';
        $end   = $escape ? ', ENT_QUOTES, \'UTF-8\')' : '';

        foreach ($rows as $row)
        {
            $this->tpl = $this->replace(
                $row['code'],
                '<?php echo ' . $begin . $this->build($row) . $end . '; ?>'
            );
        }
    }

    /**
     * @param string $key
     * @param array  $data
     * @param array  $operator
     *
     * @return mixed
     */
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

    protected function replace(string $fragment, string $code, string $tpl = null)
    {
        if (!$tpl)
        {
            $tpl = $this->tpl;
        }

        return \preg_replace(
            '~' . \preg_quote($fragment, '~') . '~u',
            $code,
            $tpl,
            1
        );
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
            $data = $this->lexeme()->data($key);

            if (true !== $data && $this->lexeme()->closed($key))
            {
                $dir = $this->popDirective($key);

                $this->tpl = $this->replace(
                    $operator['code'],
                    $dir->endDirective()
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
            $data   = $this->lexeme()->data($_token->token);

            $end = !$this->ifEnd($operator, $_token->token);

            if ($end && true !== $data)
            {
                $data = $this->lexeme()->apply(
                    $_token->token,
                    $this->fragment($operator)
                );

                /**
                 * @var Directive $directive
                 */
                $directive = $this->directive($_token->token, $data ?: [], $operator);
                $this->pushDirective($_token->token, $directive);

                $this->tpl = $this->replace(
                    $operator['code'],
                    $directive->render()
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
        $tokens    = $this->lexer()->tokens($this->tpl);

        $this->literals  = $tokens[Lexer::LITERAL];
        $this->printers  = $tokens[Lexer::PRINTER];
        $this->operators = $tokens[Lexer::OPERATOR];
        $this->rows      = $tokens[Lexer::RAW];

        $this->printers($this->printers);
        $this->printers($this->rows, false);
        $this->operators();

        // check directives
        foreach ($this->directives as $name => $items)
        {
            if ($this->lexeme()->closed($name))
            {
                if (!empty($items))
                {
                    throw new Runtime(
                        \sprintf(
                            'Directive %s not closed',
                            \get_class(Arr::pop($items))
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
