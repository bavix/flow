<?php

namespace Bavix\Flow;

use Bavix\Helpers\Arr;
use Bavix\SDK\FileLoader;

class Lexem
{

    protected $types = [
        'callable' => '[\w()]+',
        'variable' => '\w+',
        'array'    => '(array\(|\[)[\s\S]+(\]|\))',
        'ternary'  => '\X+\?\X*:\X+',
    ];

    protected $default = [
        'types' => [
            'variable'
        ]
    ];

    protected $syntax = [];
    protected $props  = [];
    protected $data   = [];
    protected $lexer;
    protected $root;

    public function __construct()
    {
        $this->root = \dirname(__DIR__, 2) . '/lexemes';
    }

    protected function lexer(): Lexer
    {
        if (!$this->lexer)
        {
            $this->lexer = new Lexer();
        }

        return $this->lexer;
    }

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

    protected function properties(array $props): array
    {
        foreach ($props as $key => $prop)
        {
            $this->property($props, $key);
        }

        return $this->props;
    }

    protected function types(array $types)
    {
        $results = [];
        foreach ($types as $type)
        {
            $results[] = $this->types[$type];
        }

        return $results;
    }

    protected function fragment(string $key): string
    {
        $property = $this->props[$key] ?? $this->default;

        return '(?<' . $key . '>(' .
            implode('|', $this->types($property['types'])) .
            '))';
    }

    protected function syntax(string $key, array $data)
    {
        $this->properties($data['properties']);

        foreach ($data['syntax'] as $text)
        {
            $tokens = $this->lexer()->tokens($text);
            $vars   = $tokens[Lexer::PRINTER];
            $code   = $text;

            foreach ($vars as $var)
            {
                $code = \str_replace(
                    $var['code'],
                    $this->fragment($var['fragment']),
                    $code
                );
            }

            $this->syntax[] = '~^' . $key . ' ' . $code . '$~ui';
        }

        return $this->syntax;
    }

    protected function get(string $key)
    {
        /**
         * @var $loader FileLoader\DataInterface
         */
        $path   = $this->root . '/' . $key;
        $loader = $this->loader($path);

        if (!$loader)
        {
            return [];
        }

        return $this->syntax(
            $key,
            $loader->asArray()
        );
    }

    public function data(string $key)
    {
        if (empty($this->data[$key]))
        {
            $this->data[$key] = $this->get($key);
        }

        return $this->data[$key];
    }

}
