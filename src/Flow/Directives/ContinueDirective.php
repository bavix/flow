<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ContinueDirective extends Directive
{

    protected function name()
    {
        return 'continue';
    }

    public function render(): string
    {
        if (isset($this->data['num']['code']))
        {
            return '<?php ' . $this->name() . ' ' . $this->data['num']['code'] . '; ?>';
        }

        return '<?php ' . $this->name() . '; ?>';
    }

}
