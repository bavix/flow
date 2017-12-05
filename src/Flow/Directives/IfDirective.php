<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class IfDirective extends Directive
{

    public function render(): string
    {
        return '<?php if (' . $this->data['args']['code'] . '):?>';
    }

    public function endDirective(): string
    {
        return '<?php endif; ?>';
    }

}
