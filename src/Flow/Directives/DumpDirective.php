<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;

class DumpDirective extends Directive
{

    public function render(): string
    {
        if (PHP_SAPI !== 'cli' && class_exists(\Symfony\Component\VarDumper\VarDumper::class))
        {
            return '<?php \Symfony\Component\VarDumper\VarDumper::dump(' . $this->data['args']['code'] . '); ?>';
        }

        return '<?php var_dump(' . $this->data['args']['code'] . '); ?>';
    }

}
