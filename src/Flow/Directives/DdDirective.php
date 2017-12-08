<?php

namespace Bavix\Flow\Directives;

class DdDirective extends DumpDirective
{

    public function render(): string
    {
        return parent::render() . '<?php die; ?>';
    }

}
