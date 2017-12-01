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
        'callable' => '[\w()]+',
        'variable' => '\w+',
        'array'    => '(array\(|\[)[\s\S]*(\]|\))',
        'ternary'  => '\X+\?\X*:\X+',
        'range'    => '\X+[\s\t]*\.\.\.[\s\t]*\X+',
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
    protected $props  = [];

    /**
     * @var array
     */
    protected $data   = [];

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
     * Lexem constructor.
     */
    public function __construct()
    {
        $this->root = \dirname(__DIR__, 2) . '/lexemes';
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
        $this->closed[$key] = $data['closed'] ?? false;
        $this->properties($data['properties']);

        $syntax = [];

        foreach ($data['syntax'] as $text)
        {
            $tokens = $this->lexer()->tokens($text);
            $vars   = $tokens[Lexer::PRINTER] ?? [];
            $code   = $text;

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
     *
     * @return array|bool
     */
    protected function get(string $key)
    {
        /**
         * @var $loader FileLoader\DataInterface
         */
        $path   = $this->root . '/' . $key;
        $loader = $this->loader($path);

        if (!$loader)
        {
            return true;
        }

        return $this->syntax(
            $key,
            $loader->asArray()
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
     *
     * @return array|bool
     */
    public function data(string $key)
    {
        if (empty($this->data[$key]))
        {
            $this->data[$key] = $this->get($key);
        }

        return $this->data[$key];
    }

}
