<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class PartialTest extends Unit
{

    public function testPartial()
    {

        $partial = <<<code
                {{ message }}
code;

        $this->eval($partial, [
            'message' => 'None'
        ]);

        $code = <<<code
    {% partial '{$this->folder}:{$this->lastView}{$this->flow->ext()}' %}
code;

        $this->assertSame(
            $this->eval($code),
            '{{ message }}'
        );

    }

}
