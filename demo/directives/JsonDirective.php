<?php

namespace Demo\Directives;

use Bavix\Flow\Directive;

class JsonDirective extends Directive
{

    public function render(): string
    {
        return '<?php echo \Bavix\Helpers\JSON::encode(' . $this->data['var']['code'] . '); ?>';
    }

}
