<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ElseDirective extends Directive
{

    public function render(): string
    {
        return '<?php else :?>';
    }

}
