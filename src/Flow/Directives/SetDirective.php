<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class SetDirective extends Directive
{

    public function render(): string
    {
        return '<?php ' . $this->data['var']['code'] . ' = ' .
            $this->data['mixed']['code'] . ';?>';
    }

}
