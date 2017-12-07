<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ForelseDirective extends Directive
{

    public function render(): string
    {
        ForDirective::else();

        return '<?php endforeach; else : ?>';
    }

}
