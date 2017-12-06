<?php

namespace Bavix\Flow;

use Bavix\Helpers\Arr;
use Bavix\Lexer\Lexer;
use Bavix\SDK\FileLoader;

/**
 * Class Lexem
 *
 * @package Bavix\Flow
 */
class Lexem
{

    /**
     * @var array
     */
    protected $types = [
        'callable' => '[.\w\s(,\'")]+',
        'variable' => '[\w\'":.\[\]()\s]+',
        'array'    => '(array\(|\[)[\s\S]*(\]|\))',
        'ternary'  => '\X+\?\X*:\X+',
        'any'      => '\X+',
    ];

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
     * @var Lexer
     */
    protected $lexer;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var Flow
     */
    protected $flow;

    /**
     * Lexem constructor.
     *
     * @param Flow $flow
     * @param string $root
     */
    public function __construct(Flow $flow, string $root = null)
    {
        if (!$root)
        {
            $root = \dirname(__DIR__, 2);
        }

        $this->root = $root . '/lexemes';
        $this->flow = $flow;
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
        foreach (FileLoader::extensions() as $ext)
        {
            try
            {
                return FileLoader::load($file . '.' . $ext);
            }
            catch (\Throwable $throwable)
            {

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
                $extends = Arr::map((array)$prop['extends'], function ($extend) use ($self, $props) {
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
     * @param string $key
     * @param array  $data
     *
     * @return array|bool
     */
    protected function get(string $key, array $data = null)
    {
        /**
         * @var $loader FileLoader\DataInterface
         */
        $path   = $this->root . '/' . $key;
        $loader = $this->loader($path)?:$data;

        if (null === $loader)
        {
            return true;
        }

        return $this->syntax(
            $key,
            (\is_array($loader) ?
                $loader :
                $loader->asArray()) ?: []
        );
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function closed(string $key): bool
    {
        $this->get($key);

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
        if (!\array_key_exists($key, $this->data))
        {
            $this->data[$key] = $this->get($key, $data);
        }

        return $this->data[$key];
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

        $lexer = new Lexer();
        $flow  = $this->flow;

        foreach ($lexData as $datum)
        {
            if (\preg_match($datum['regexp'], $tpl, $outs))
            {
                $data = Arr::filter($outs, function (...$args) {
                    return \is_string(\end($args));
                });

                $data = Arr::map($data, function ($value) use ($lexer, $flow) {
                    $value  = '{{ ' . $value . ' }}';
                    $tokens = $lexer->tokens($value);
                    $_lexer = \current($tokens[Lexer::PRINTER]);

                    return [
                        'lexer' => $_lexer,
                        'code'  => $flow->build($_lexer)
                    ];
                });

                break;
            }
        }

        return $data;
    }

}
