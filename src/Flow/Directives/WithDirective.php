<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;
use Bavix\Helpers\Arr;

class WithDirective extends Directive
{

    protected static $storage = [];

    public static function last()
    {
        return current(static::$storage);
    }

    public static function push($data)
    {
        Arr::push(static::$storage, $data);
        end(static::$storage);
    }

    public static function pop()
    {
        Arr::pop(static::$storage);
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
