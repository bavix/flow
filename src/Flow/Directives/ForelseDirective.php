<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ForelseDirective extends Directive
{

    public function render(): string
    {
        $variable = '${\'' . __CLASS__ . '\'}';

        return '<?php endforeach; else : ' . $variable . ' = (' . $variable. ' ?? 0) + 1; ?>';
    }

}
