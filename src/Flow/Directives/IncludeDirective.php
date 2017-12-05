<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class IncludeDirective extends Directive
{

    public function render(): string
    {
        return '<?php include $this->flow->path(' . $this->data['path']['code'] . ')?>';
    }

}
