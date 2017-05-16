<?php

namespace Bavix\Flow;

class Validator
{

    const T_EQUAL = PHP_INT_MAX;

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
