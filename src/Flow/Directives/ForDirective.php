<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;
use Bavix\Flow\Loop;

class ForDirective extends Directive
{

    protected static $loops = [];

    public static function loop($key, $rows = null)
    {
        if (!\array_key_exists($key, static::$loops))
        {
            static::$loops[$key] = new Loop($rows);
        }

        return static::$loops[$key];
    }

    public function render(): string
    {
        $init = '';
        $rows = $this->data['rows']['code'];

        if (\count($this->data['rows']['lexer']['tokens']) > 1)
        {
            $rows = $this->randVariable();
            $init = $rows . '=' . $this->data['rows']['code'] . ';';
        }

        $loop = $this->randVariable();
        $key  = $this->data['key']['code'] ?? $this->randVariable();
        $row  = $this->data['row']['code'];

        return '<?php ' . $init . 'if (!empty(' . $rows . ')):' .
            '\Bavix\Flow\Directives\ForDirective::loop(\'' . $loop . '\', ' . $rows . ');' .
            'foreach (' . $rows . ' as ' . $key . ' => ' . $row . '): ' .
            '$loop = \Bavix\Flow\Directives\ForDirective::loop(\'' . $loop . '\');' .
            '$loop->next(' . $key . ');?>';
    }

    public function endDirective(): string
    {
        if (ForelseDirective::get())
        {
            ForelseDirective::pop();
            return '<?php unset($loop); endif; ?>';
        }

        return '<?php unset($loop); endforeach; endif; ?>';
    }

}
