<?php

namespace Bavix\Flow\Directives;

use Bavix\Flow\Directive;
use Bavix\FlowNative\Extensions\Blocks;

class SectionDirective extends Directive
{

    protected $extensions = [
        'reset'   => Blocks::RESET,
        'append'  => Blocks::APPEND,
        'prepend' => Blocks::PREPEND,
    ];

    /**
     * @return string
     */
    public function render(): string
    {
        $name = $this->data['name']['lexer']['fragment'];
        $type = $this->data['type']['lexer']['fragment'] ?? 'reset';

        return '<?php $this->ext->blocks()->start(\'' . $name . '\', \'' . $this->extensions[$type] . '\')?>';
    }

    /**
     * @return string
     */
    public function endDirective(): string
    {
        return '<?php echo $this->ext->blocks()->end()?>';
    }

}
