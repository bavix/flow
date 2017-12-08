<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class UseDirective extends Directive
{

    public function render(): string
    {
        return '<?php use ' . $this->data['args']['code'] . '; ?>';
    }

}
