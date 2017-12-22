<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class WithTest extends Unit
{

    public function testWith()
    {
        $messages = [
            'msg',
            'no msg'
        ];

        $code = <<<code
        {% with messages %}
            {{ .[0] }}
        {% endwith %}
code;

        $this->assertSame(
            $this->eval($code, [
                'messages' => $messages
            ]),
            $messages[0]
        );
    }

}
