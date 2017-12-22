<?php

namespace Bavix\Flow;

use Bavix\Helpers\Arr;
use Bavix\Lexer\Lexer;
use Bavix\SDK\FileLoader;

/**
 * Class Lexeme
 *
 * @package Bavix\Flow
 */
class Lexeme
{

    /**
     * @var array
     */
    protected $types;

    /**
     * @var array
     */
    protected $default = [
        'types' => [
            'variable'
        ]
    ];

    /**
     * @var array
     */
    protected $props = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $closed = [];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var string[]
     */
    protected $folders = [];

    /**
     * @var Lexer
     */
    protected $lexer;

    /**
     * @var Flow
     */
    protected $flow;

    /**
     * Lexeme constructor.
     *
     * @param Flow $flow
     */
    public function __construct(Flow $flow)
    {
        $this->types = Property::get('types');
        $this->addFolder(\dirname(__DIR__, 2) . '/lexemes');
        $this->flow = $flow;
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public function addFolder(string $path): self
    {
        Arr::unShift($this->folders, $path);

        return $this;
    }

    /**
     * @return Lexer
     */
    protected function lexer(): Lexer
    {
        if (!$this->lexer)
        {
            $this->lexer = new Lexer();
        }

        return $this->lexer;
    }

    /**
     * @param $file
     *
     * @return FileLoader\DataInterface|null
     */
    protected function loader($file)
    {
        foreach ($this->folders as $folder)
        {
            foreach (FileLoader::extensions() as $ext)
            {
                try
                {
                    return FileLoader::load($folder . '/' . $file . '.' . $ext);
//                    $data = FileLoader::load($folder . '/' . $file . '.' . $ext);
//
//                    if ($ext === 'yml')
//                    {
//                        (new FileLoader\PHPLoader($folder . '/' . $file . '.php' ))
//                            ->save($data->asArray());
//                    }
//
//                    return $data;
                }
                catch (\Throwable $throwable)
                {
                    // skip...
                }
            }
        }

        return null;
    }

    /**
     * @param array  $props
     * @param string $key
     *
     * @return array
     */
    public function property(array $props, string $key): array
    {
        $self = $this;
        $prop = &$props[$key];

        if (empty($this->props[$key]))
        {
            if (isset($prop['extends']))
            {
                $extends = Arr::map((array)$prop['extends'], function ($extend) use ($self, &$props) {
                    return $self->property($props, $extend);
                });

                $prop = \array_merge_recursive(
                    $prop,
                    ...$extends
                );
            }

            $this->props[$key] = $prop;
        }

        return $this->props[$key];
    }

    /**
     * @param array $props
     *
     * @return array
     */
    protected function properties(array $props): array
    {
        foreach ($props as $key => $prop)
        {
            $this->property($props, $key);
        }

        return $this->props;
    }

    /**
     * @param array $types
     *
     * @return array
     */
    protected function types(array $types): array
    {
        $results = [];
        foreach ($types as $type)
        {
            $results[] = $this->types[$type];
        }

        return $results;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function fragment(string $key): string
    {
        $property = $this->props[$key] ?? $this->default;

        return '(?<' . $key . '>(' .
            implode('|', $this->types($property['types'])) .
            '))';
    }

    /**
     * @param string $key
     * @param array  $syntax
     *
     * @return array
     */
    public function syntax2Array(string $key, array $syntax): array
    {
        $props  = $this->props[$key] ?? null;
        $closed = $this->closed[$key] ?? false;

        return Cache::get(__CLASS__ . $key, function () use ($syntax, $props, $closed) {
            return [
                'syntax' => $syntax,
                'props'  => $props,
                'closed' => $closed,
            ];
        });
    }

    protected function tryLoad(string $key)
    {
        if (empty($this->data[$key]))
        {
            $item = Cache::getItem(__CLASS__ . $key);

            if ($item && $item->isHit())
            {
                $_cache = $item->get();

                $this->data[$key]   = $_cache['syntax'];
                $this->props[$key]  = $_cache['props'];
                $this->closed[$key] = $_cache['closed'];

                return $this->data[$key];
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @param array  $data
     *
     * @return array|mixed
     */
    protected function getLexemes(string $key, array $data)
    {
        $syntax = $this->tryLoad($key);

        if (empty($this->data[$key]))
        {
            $syntax = $this->syntax($key, $data);
            $this->syntax2Array($key, $syntax);
        }

        return $syntax;
    }

    /**
     * @param string $key
     * @param array  $data
     *
     * @return array
     */
    protected function syntax(string $key, array $data)
    {
        foreach ($data['directives'] ?? [] as $_key => $directive)
        {
            $this->data($_key, $directive ?: []);
        }

        $this->closed[$key] = $data['closed'] ?? false;
        $this->properties($data['properties'] ?? []);

        $syntax = [];

        foreach ($data['syntax'] ?? [] as $text)
        {
            $tokens = $this->lexer()->tokens($text);
            $vars   = $tokens[Lexer::PRINTER] ?? [];
            $code   = \str_replace(
                ['\\(', '\\)', ','],
                ['\\( ', ' \\)', ' ,'],
                $text
            );

            foreach ($vars as $var)
            {
                $code = \str_replace(
                    $var['code'],
                    $this->fragment($var['fragment']),
                    $code
                );
            }

            $syntax[] = [
                'vars'   => $vars,
                'regexp' => '~^' . $key . ' ' . $code . '$~ui'
            ];
        }

        return $syntax;
    }

    /**
     * @param string     $key
     * @param array|null $data
     *
     * @return array|bool|mixed
     */
    protected function get(string $key, array $data = null)
    {
        /**
         * @var $loader mixed
         */
        $loader = $this->loader($key) ?: $data;

        if (null === $loader)
        {
            return true;
        }

        $mixed = [];

        if ($loader)
        {
            $mixed = $loader;

            if (\is_object($mixed))
            {
                $mixed = $loader->asArray();
            }
        }

        return $this->getLexemes($key, $mixed);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function closed(string $key): bool
    {
        $this->data($key);

        return $this->closed[$key] ?? false;
    }

    /**
     * @param string $key
     * @param array  $data
     *
     * @return array|bool
     */
    public function data(string $key, array $data = null)
    {
        $this->tryLoad($key);

        if (!\array_key_exists($key, $this->data))
        {
            $this->data[$key] = $this->get($key, $data);
        }

        return $this->data[$key];
    }

    /**
     * @param string $value
     *
     * @return array
     */
    public function lexerApply(string $value): array
    {
        $name = __FUNCTION__ . $value;
        $item = Cache::getItem($name);

        if ($item && $item->isHit())
        {
            return $item->get();
        }

        $value  = '{{ ' . $value . ' }}';
        $tokens = $this->flow->lexer()->tokens($value);
        $_lexer = \current($tokens[Lexer::PRINTER]);

        $store = [
            'lexer' => $_lexer,
            'code'  => $this->flow->build($_lexer)
        ];

        return Cache::get($name, function () use ($store) {
            return $store;
        });
    }

    /**
     * @param string $key
     * @param string $tpl
     *
     * @return array|null
     */
    public function apply(string $key, string $tpl)
    {
        $lexData = $this->data($key);
        $data    = null;

        if (true === $lexData)
        {
            return $data;
        }

        foreach ($lexData as $datum)
        {
            if (\preg_match($datum['regexp'], $tpl, $outs))
            {
                $data = Arr::filter($outs, function (...$args) {
                    return \is_string(\end($args));
                });

                $data = Arr::map($data, [$this, 'lexerApply']);
                break;
            }
        }

        return $data;
    }

}
