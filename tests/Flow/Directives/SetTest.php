<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class SetTest extends Unit
{

    public function testSet()
    {
        $hello = 'hello world';

        $code = <<<code
        {% set hello = '{$hello}' %}
        {{ hello }}
code;

        $this->assertSame(
            $this->eval($code),
            $hello
        );
    }

}
