<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ForelseDirective extends Directive
{

    protected static $else = 0;

    public static function push()
    {
        static::$else++;
    }

    public static function pop()
    {
        static::$else--;
    }

    public static function get()
    {
        return static::$else;
    }

    public function render(): string
    {
        static::push();

        return '<?php endforeach; else : ?>';
    }

}
