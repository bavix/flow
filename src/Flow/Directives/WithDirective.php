<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;
use Bavix\Helpers\Arr;

class WithDirective extends Directive
{

    protected static $index   = 0;
    protected static $storage = [];

    protected static function calc()
    {
        static::$index     = \count(static::$storage) - 1;
    }

    public static function &last()
    {
        return static::$storage[static::$index];
    }

    public static function pushObject($data)
    {
        static::$storage[] = $data;
        static::$index     = \count(static::$storage) - 1;
    }

    public static function push(&$data)
    {
        static::$storage[] = &$data;
        static::$index     = \count(static::$storage) - 1;
    }

    public static function pop()
    {
        if (!empty(static::$storage))
        {
            Arr::pop(static::$storage);
            static::$index--;
        }
    }

    public function render(): string
    {
        return '<?php \\' . __CLASS__ . '::push(' . $this->data['name']['code'] . ')?>';
    }

    public function endDirective(): string
    {
        return '<?php \\' . __CLASS__ . '::pop()?>';
    }

}
