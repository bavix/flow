<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;
use Bavix\Flow\Loop;

class ForDirective extends Directive
{

    protected static $loops = [];

    public static function loop($key, $rows = null)
    {
        if (empty($loops[$key]))
        {
            $loops[$key] = new Loop($rows);
        }

        return $loops[$key];
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
        $variable = '${\'' . ForelseDirective::class . '\'}';

        return '<?php if (empty(' . $variable . ')) { endforeach; unset(' . $variable . '); } endif; ?>';
    }

}
