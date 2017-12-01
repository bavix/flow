<?php

namespace Flow\Directives;

use Flow\Directive;

class ForDirective extends Directive
{

    public function rows()
    {
        // get rows
    }

    public function key()
    {
        // get key
    }

    public function row()
    {
        // get row
    }

    public function render(): string
    {
        return 'if (!empty(' . $this->rows() . ')) :' .
            'foreach (' . $this->rows() . ' as ' . $this->key() . ' => ' . $this->row() . ') :';
    }

    public function endDirective(): string
    {
        return 'endforeach; endif;';
    }

}
