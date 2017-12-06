<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class ExtendsDirective extends Directive
{

    public function render(): string
    {
        return '<?php $this->ext->blocks()->extends($this->flow->path(' . $this->data['path']['code'] . '))?>';
    }

}
