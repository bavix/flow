<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ElseifDirective extends Directive
{

    public function render(): string
    {
        return '<?php elseif (' . $this->data['args']['code'] . ') :?>';
    }

}
