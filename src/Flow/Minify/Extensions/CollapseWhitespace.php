<?php

namespace Bavix\Flow\Minify\Extensions;

use Bavix\Flow\Minify\Extension;

class CollapseWhitespace extends Extension
{

    /**
     * @return array
     */
    public function patterns(): array
    {
        return [
            '\n+([\S]*)'         => '$1',
            '\r'                => '',
            "[\s\t\n\r\0\x0B]+" => ' ',
        ];
    }

}
