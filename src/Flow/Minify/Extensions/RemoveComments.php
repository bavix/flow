<?php

namespace Bavix\Flow\Minify\Extensions;

use Bavix\Flow\Minify\Extension;

class RemoveComments extends Extension
{

    /**
     * @return array
     */
    public function patterns(): array
    {
        return [
            '<!--\X*?-->' => ''
        ];
    }

}
