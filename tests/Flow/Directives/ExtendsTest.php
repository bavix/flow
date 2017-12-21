<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class ExtendsTest extends Unit
{

    public function testExtends()
    {
        $layout = <<<code
    {%block hello%}{%endblock%}
code;

        $this->eval($layout);

        $block = <<<code
    {% extends '{$this->folder}:{$this->lastView}' %}
    
    {% block hello %}
        <h1>{{ message }}</h1>
    {% endblock %}
code;

        $message = 'flow';
        $results = $this->eval($block, [
            'message' => 'flow'
        ]);

        $this->assertSame(
            $results,
            "<h1>$message</h1>"
        );
    }

}
