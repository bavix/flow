<?php

namespace Bavix\Flow;

use Bavix\Foundation\Arrays\Queue;

class Lexer
{

    const OPERATOR = 1;
    const PRINTER  = 2;

    /**
     * @var array
     */
    protected $markers = [
        self::OPERATOR => ['%', '%'],
        self::PRINTER  => ['{', '}'],
    ];

    /**
     * @var array
     */
    protected $phpTags = [
        '<?php' => '<!--',
        '<?'    => '<!--',
        '?>'    => '-->',
    ];

    /**
     * @var array
     */
    protected $operatorTags = [
        '{%' => '<?php' . PHP_EOL,
        '%}' => '?>',
    ];

    /**
     * @var array
     */
    protected $printTags = [
        '{{{' => '<?php' . PHP_EOL . '{',
        '{{'  => '<?php' . PHP_EOL,
        '}}}' => '}?>',
        '}}'  => '?>',
    ];

    /**
     * @param string $source
     *
     * @return array
     */
    public function parse(&$source)
    {
        $source   = preg_replace('~\{\*\X*?\*\}~', '', $source);
        $operator = strtr($source, array_merge($this->phpTags, $this->operatorTags));
        $print    = strtr($source, array_merge($this->phpTags, $this->printTags));

        // https://php.ru/manual/tokens.html
        $operatorData = \token_get_all($operator);
        $printData    = \token_get_all($print);

        return array_merge(
            $this->maker($operatorData, self::OPERATOR),
            $this->maker($printData, self::PRINTER)
        );
    }

    /**
     * @param array $_
     * @param int   $marker
     *
     * @return array
     */
    protected function maker(array $_, $marker)
    {
        $queue = new Queue($_);
        $list  = [];

        while (!$queue->isEmpty())
        {
            $tokens = [];

            $php = $queue->pop(); // <?php
            $php = $php[0] ?? $php;

            if ($php !== \T_OPEN_TAG)
            {
                continue;
            }

            $key = '';

            do
            {
                $read = $queue->pop();

                $type = $read[0] ?? \T_STRING;
                $data = $read[1] ?? $read;

                if ($type === \T_CLOSE_TAG || $queue->isEmpty())
                {
                    break;
                }

                $key .= $data;

                if ($type === \T_WHITESPACE)
                {
                    continue;
                }

                $tokens[] = $data;
            }
            while (true);

            $list['{' . $this->markers[$marker][0] . $key . $this->markers[$marker][1] . '}'] = [
                'type'     => $marker,
                'template' => $key,
                'code'     => implode(' ', $tokens),
                'tokens'   => $tokens
            ];
        }

        return $list;
    }

}
