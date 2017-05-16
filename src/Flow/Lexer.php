<?php

namespace Bavix\Flow;

use Bavix\Exceptions;
use Bavix\Foundation\Arrays\Queue;

class Lexer
{

    const RAW      = 1;
    const OPERATOR = 2;
    const PRINTER  = 4;
    const LITERAL  = 8;

    protected $openLiteralRegExp  = "\{%[ \t\n\r \v]*literal[ \t\n\r \v]*%\}";
    protected $closeLiteralRegExp = "\{%[ \t\n\r \v]*endliteral[ \t\n\r \v]*%\}";

    protected $literals = [];

    /**
     * @var array
     */
    protected $phpTags = [
        '<?php' => '<!--',
        '<?='   => '<!--',
        '<?'    => '<!--',
        '?>'    => '-->',
    ];

    protected function tokens(array $tokens)
    {
        $queue = new Queue($tokens);
        $queue->pop(); // remove open <?php

        $open = [
            // open
            '{!' => self::RAW,
            '{%' => self::OPERATOR,
            '{{' => self::PRINTER,
        ];

        $close = [
            // close
            '!}' => self::RAW,
            '%}' => self::OPERATOR,
            '}}' => self::PRINTER,
        ];

        $begin = array_flip($open);

        $end = [
            self::RAW      => '!',
            self::OPERATOR => '%',
            self::PRINTER  => '}',
        ];

        $storage = [
            self::RAW      => [],
            self::OPERATOR => [],
            self::PRINTER  => [],
        ];

        $literals   = [];
        $lvlLiteral = 0;

        $lastChar = null;
        $start    = null;
        $mixed    = [];
        $last     = null;
        $key      = '';

        while (!$queue->isEmpty())
        {
            $read = $queue->pop();

            $type = is_array($read) ? $read[0] : \T_STRING;
            $data = $read[1] ?? $read;

            $key .= $data;

            if ($type === \T_WHITESPACE)
            {
                $lastChar = $data;
                continue;
            }

            if (!$start && $data === '{' && $key !== '{{')
            {
                $key = $data;
            }

            $index = $lastChar . $data;

            if ((isset($open[$index]) && $start) || (isset($close[$index]) && !$start))
            {
                throw new Exceptions\Logic('Syntax error `' . $lastChar . $data . '`');
            }

            if (isset($open[$index]))
            {
                $start = $open[$lastChar . $data];
            }
            else if (isset($close[$index]))
            {
                if ($start !== $close[$lastChar . $data])
                {
                    throw new Exceptions\Runtime(
                        'Undefined syntax code `' . $begin[$start] . ' ' . implode(' ', $mixed) . $data . '`');
                }

                if (empty($mixed))
                {
                    throw new Exceptions\Blank('Empty tokens `' . $key . '`');
                }

                $token = current($mixed);
                $name  = $token->name;

                $storage[$start][$key] = [
                    'type'   => $start,
                    'name'   => $name,
                    'tpl'    => $key,
                    'code'   => implode(' ', $mixed),
                    'tokens' => $mixed
                ];

                $mixed = [];
                $start = null;
                $last  = null;
                $key   = '';
            }
            else if ($start && $end[$start] !== $data)
            {
                if (
                    // last exists
                    $last &&

                    // if exists then type is string?
                    $last->type === \T_STRING &&

                    // if type is string then data is '('?
                    $data === '(' &&

                    // if true then token is variable ?
                    preg_match('~[a-z_]+~i', $last->token)
                )
                {
                    $last->type = \T_FUNCTION;
                }

                $mixed[] = $last = new Token($data, $type);
            }

            $lastChar = $data;
        }

        $storage[self::LITERAL] = $this->literals;
        $this->literals         = [];

        return $storage;
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    protected function literal(array $matches)
    {
        $hash = '[!' . __FUNCTION__ . '_' . crc32($matches[1]) . '!]';

        $this->literals[$hash] = $matches[1];

        return $hash;
    }

    /**
     * @param string $source
     *
     * @return array
     */
    public function parse(&$source)
    {
        $source = \preg_replace_callback(
            "~{$this->openLiteralRegExp}(\X*?){$this->closeLiteralRegExp}~u",
            [$this, 'literal'],
            $source
        );

        // check literal open
        if (preg_match("~{$this->openLiteralRegExp}~u", $source))
        {
            throw new Exceptions\Logic('Literal isn\'t closed');
        }

        // check literal close
        if (preg_match("~{$this->closeLiteralRegExp}~u", $source))
        {
            throw new Exceptions\Logic('Literal isn\'t open');
        }

        $source = \preg_replace('~\{\*\X*?\*\}~', '', $source);
        $source = \strtr($source, $this->phpTags);

        return $this->tokens(
            \token_get_all('<?php' . PHP_EOL . $source)
        );
    }

}
