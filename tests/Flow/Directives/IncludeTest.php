<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class IncludeTest extends Unit
{

    public function testInclude()
    {

        $message = 'Hello World';

        $include = <<<code
                {{ message }}
code;

        $this->eval($include, [
            'message' => 'None'
        ]);

        $code = <<<code
    {% include '{$this->folder}:{$this->lastView}' %}
code;

        $this->assertSame(
            $this->eval($code, [
                'message' => $message
            ]),

            $message
        );

    }

}
