<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class PartialDirective extends Directive
{

    public function render(): string
    {
        return '<?php echo \file_get_content($this->flow->path(' . $this->data['path']['code'] . '))?>';
    }

}
