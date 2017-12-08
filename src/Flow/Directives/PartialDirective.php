<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class PartialDirective extends Directive
{

    public function render(): string
    {
        return '<?php echo \file_get_contents($this->native->path(' .
            $this->data['path']['code'] .
            '.\'' . $this->flow->ext() . '\'))?>';
    }

}
