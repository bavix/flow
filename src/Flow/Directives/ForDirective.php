<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ForDirective extends Directive
{

    public function render(): string
    {
        $init = '';
        $rows = $this->data['rows']['code'];

        if (\count($this->data['rows']['lexer']['tokens']) > 1)
        {
            $rows = $this->randVariable();
            $init = $rows . '=' . $this->data['rows']['code'] . ';';
        }

        $key = $this->data['key']['code'] ?? $this->randVariable();
        $row = $this->data['row']['code'];

        return '<?php ' . $init . 'if (!empty(' . $rows . ')):' .
            'foreach (' . $rows . ' as ' . $key . ' => ' . $row . '):?>';
    }

    public function endDirective(): string
    {
        return '<?php endforeach; endif; ?>';
    }

}
