<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class UseTest extends Unit
{

    public function testSet()
    {
        $hello = 'PHP: Hello World';

        $code = <<<code
        {% use \Bavix\Helpers\Str %}
        {{ Str::sub(message, 0, 3) }}
code;

        $this->assertSame(
            $this->eval($code, [
                'message' => $hello
            ]),
            'PHP'
        );
    }

}
