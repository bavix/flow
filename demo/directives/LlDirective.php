<?php

namespace Demo\Directives;

use Bavix\Flow\Directive;

class LlDirective extends Directive
{

    public function render(): string
    {
        return '<ul><?php foreach (' . $this->data['var']['code'] . ' as $item) : ?>' .
            '<li><?=$item?></li><?php endforeach; ?></ul>';
    }

}
