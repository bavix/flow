<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class DumpDirective extends Directive
{

    public function render(): string
    {
        return '<?php var_dump(' . $this->data['args']['code'] . '); ?>';
    }

}
