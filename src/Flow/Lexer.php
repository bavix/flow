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

    /**
     * @var string
     */
    protected $openLiteralRegExp  = "\{%[ \t\n\r \v]*literal[ \t\n\r \v]*%\}";

    /**
     * @var string
     */
    protected $closeLiteralRegExp = "\{%[ \t\n\r \v]*endliteral[ \t\n\r \v]*%\}";

    /**
     * @var array
     */
    protected $literals = [];

    /**
     * @var array
     */
    protected $prints = [
        self::OPERATOR => false,
        self::RAW      => true,
        self::PRINTER  => true,
    ];

    /**
     * @var array
     */
    protected $escaping = [
        self::OPERATOR => false,
        self::RAW      => false,
        self::PRINTER  => true,
    ];

    /**
     * @var array
     */
    protected $phpTags = [
        '<?php' => '<!--',
        '<?='   => '<!--',
        '<?'    => '<!--',
        '?>'    => '-->',
    ];

    protected function last($last, $data, $equal = '.')
    {
        return
            // last exists
            $last &&

            // if exists then type is string?
            in_array($last->type, [\T_STRING, \T_VARIABLE], true) &&

            // if type is string then data is '('?
            $data === $equal &&

            // if true then token is variable ?
            preg_match('~[a-z_]+~i', $last->token);
    }

    protected function analysis(array $tokens)
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

        $anyType  = null;
        $lastChar = null;
        $type     = null;
        $mixed    = [];
        $last     = null;
        $dot      = null;
        $code     = '';
        $print    = null;

        while (!$queue->isEmpty())
        {
            $read = $queue->pop();

            $_type = Validator::getValue($read);
            $data  = $read[1] ?? $read;

            if ($_type === \T_OPEN_TAG || $_type === \T_OPEN_TAG_WITH_ECHO || $_type === \T_CLOSE_TAG)
            {
                continue;
            }

            if ($data === '=')
            {
                $print = false;
            }

            if ($_type === \T_STRING)
            {
                $isVar = preg_match('~[a-z_]+[\w_]*~i', $data);

                $_type = Validator::getType($data, $isVar ? \T_VARIABLE : \T_STRING);

                if ($isVar && !empty($mixed))
                {
                    $mix = current($mixed);

                    if ($mix->type === \T_FOR && $data === 'in')
                    {
                        $_type = Validator::get('T_FOR_IN');
                    }
                }
            }

            $code .= $data;

            if (($dot || $data === '.') && $anyType === \T_WHITESPACE)
            {
                throw new Exceptions\Runtime('Undefined dot `' . implode(' ', $mixed) . ' ' . $data . '`');
            }

            if ($_type === \T_WHITESPACE)
            {
                $lastChar = $data;
                $anyType  = $_type;
                continue;
            }

            $anyType = $_type;

            if (!$type && $data === '{' && $code !== '{{')
            {
                $code = $data;
            }

            if ($data{0} === '\\')
            {
                throw new \ParseError('namespace isn\'t resolved');
            }

            $index = $lastChar . $data;

            if ((isset($open[$index]) && $type) || (isset($close[$index]) && !$type))
            {
                throw new Exceptions\Logic('Syntax error `' . $lastChar . $data . '`');
            }

            if (isset($open[$index]))
            {
                if ($dot)
                {
                    throw new Exceptions\Runtime('Undefined dot');
                }

                $type  = $open[$lastChar . $data];
                $print = $this->prints[$type];
            }
            else if (isset($close[$index]))
            {
                if ($dot)
                {
                    throw new Exceptions\Runtime('Undefined dot `' . \implode(' ', $mixed) . '`');
                }

                if ($type !== $close[$lastChar . $data])
                {
                    throw new Exceptions\Runtime(
                        'Undefined syntax code `' . $begin[$type] . ' ' . \implode(' ', $mixed) . $data . '`');
                }

                if (empty($mixed))
                {
                    throw new Exceptions\Blank('Empty tokens `' . $code . '`');
                }

                $token    = current($mixed);
                $name     = $token->name;
                $fragment = \preg_replace('~[ \t\n\r\v]{2,}~', ' ', $code);

                $storage[$type][$code] = [
                    'type'     => $type,
                    'print'    => $print,
                    'escape'   => $this->escaping[$type],
                    'name'     => $name,
                    'code'     => $code,
                    'fragment' => \trim(\mb_substr($fragment, 2, -2)),
                    'tokens'   => $mixed
                ];

                $mixed = [];
                $type  = null;
                $last  = null;
                $code  = '';
            }
            else if ($type && $end[$type] !== $data)
            {
                if ($this->last($last, $data, '('))
                {
                    $last->type = \T_FUNCTION;
                }
                else if ($this->last($last, $data, '.') || $dot)
                {
                    $dot         = !$dot;
                    $last->token .= $data;

                    continue;
                }

                $mixed[] = $last = new Token($data, $_type);
            }

            $lastChar = $data;
        }

        // set literal & cleanup literals
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
        // hash from matches
        $hash = '[!' . __FUNCTION__ . '::read(' . \crc32($matches[1]) . ')!]';

        // save hash and value to literals array
        $this->literals[$hash] = $matches[1];

        // return hash value for replace
        return $hash;
    }

    /**
     * @param string $source
     *
     * @return array
     */
    public function tokens(&$source)
    {
        // literal from source to array
        $source = \preg_replace_callback(
            "~{$this->openLiteralRegExp}(\X*?){$this->closeLiteralRegExp}~u",
            [$this, 'literal'],
            $source
        );

        // if check literal open then throw
        if (\preg_match("~{$this->openLiteralRegExp}~u", $source))
        {
            throw new Exceptions\Logic('Literal isn\'t closed');
        }

        // if check literal close then throw
        if (\preg_match("~{$this->closeLiteralRegExp}~u", $source))
        {
            throw new Exceptions\Logic('Literal isn\'t open');
        }

        // remove comments
        $source  = \preg_replace('~\{(?<q>\*|#)\X*?(\k<q>)\}~', '', $source);
        $source  = \strtr($source, $this->phpTags); // remove php tags
        $lexCode = \preg_replace('~(["\'#]{1}|\/{2}|\/\*)~u', '?>$1<?php ', $source);

        // analysis tokens
        return $this->analysis(
            // source progress with helped tokenizer
            \token_get_all('<?php' . PHP_EOL . $lexCode)
        );
    }

}
