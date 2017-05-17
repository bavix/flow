<?php

namespace Bavix\Flow;

class Validator
{

    const T_EQUAL      = PHP_INT_MAX;
    const T_FOR_IN     = self::T_EQUAL - 1;
    const T_NULL       = self::T_FOR_IN - 1;
    const T_BRACKET    = self::T_NULL - 1;
    const T_ENDBRACKET = self::T_BRACKET - 1;
    const T_ENDARRAY   = self::T_ENDBRACKET - 1;

    const T_HELPER    = self::T_ENDARRAY - 1;
    const T_ENDHELPER = self::T_HELPER - 1;

    protected static $types = [
        '['    => \T_ARRAY,
        ']'    => self::T_ENDARRAY,
        '='    => self::T_EQUAL,
        'null' => self::T_NULL,
        '('    => self::T_BRACKET,
        ')'    => self::T_ENDBRACKET,
    ];

    protected static $lexerTypes = [
        Lexer::OPERATOR => [
            'helper'    => self::T_HELPER,
            'endhelper' => self::T_ENDHELPER,
        ],
    ];

    /**
     * @param array|string $type
     *
     * @return int|string
     */
    public static function getValue($type)
    {

        if (!is_array($type))
        {
            return \T_STRING;
        }

        return $type[0];
    }

    /**
     * @return array
     */
    protected static function constants()
    {
        static $_;

        if (!$_)
        {
            $ref = new \ReflectionClass(static::class);
            $_   = $ref->getConstants();
        }

        return $_;
    }

    public static function getType($value, $default, $lexerType)
    {
        if ($lexerType && isset(static::$lexerTypes[$lexerType][$value]))
        {
            return static::$lexerTypes[$lexerType][$value];
        }

        if (isset(static::$types[$value]))
        {
            return static::$types[$value];
        }

        return $default;
    }

    /**
     * @param $type
     *
     * @return int|string
     */
    public static function get($type)
    {
        if (\is_string($type))
        {
            if (\defined($type))
            {
                return \constant($type);
            }

            if (\defined(static::class . '::' . $type))
            {
                return \constant(static::class . '::' . $type);
            }

            return \T_STRING;
        }

        $token = \token_name($type);

        if ($token === 'UNKNOWN')
        {
            foreach (static::constants() as $name => $value)
            {
                if ($value === $type)
                {
                    return $name;
                }
            }

            return 'T_STRING';
        }

        return $token;
    }

    public function valid(array $tokens)
    {

    }

}
