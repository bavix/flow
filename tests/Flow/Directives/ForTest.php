<?php

namespace Tests\Flow\Directives;

use Bavix\Helpers\Arr;
use Tests\Unit;

class ForTest extends Unit
{

    public function testFor()
    {
        $data = [1, 2, 3];
        $code = <<<code
    {% for datum in data %}{{ datum }}{% endfor %}
code;

        $results = $this->eval($code, [
            'data' => $data
        ]);

        $this->assertSame(
            \preg_replace('~\D~', '', $results),
            implode($data)
        );
    }

    public function testForelse()
    {
        $data = [1, 2, 3];

        $code = <<<code
    {% 
    for datum in data 
    
    
    %}          {{ datum }}{% forelse %}Empty{% endfor %}
code;

        $digits = $this->eval($code, [
            'data' => $data
        ]);

        $empty = $this->eval($code);

        $this->assertSame(
            \preg_replace('~\W~', '', $digits),
            implode($data)
        );

        $this->assertSame(
            \preg_replace('~\W~', '', $empty),
            'Empty'
        );
    }

    public function testBreak()
    {
        $data = range(1, 100);
        $count = 5;

        $code = <<<code
    {% for datum in data %}
        {% if datum > count %} {% break %} {%endif%}
        {{ datum }}
    {% endfor %}
code;

        $results = $this->eval($code, [
            'data' => $data,
            'count' => $count
        ]);

        $arrSlice = \array_slice($data, 0, $count);

        $this->assertSame(
            \preg_replace('~\W~', '', $results),
            implode($arrSlice)
        );
    }

    public function testContinue()
    {
        $data = range(1, 100);
        $mod = 5;

        $code = <<<code
    {% for datum in data %}
        {% if !(datum % mod) %} {% continue %} {%endif%}
        {{ datum }}
    {% endfor %}
code;

        $results = $this->eval($code, [
            'data' => $data,
            'mod' => $mod
        ]);

        $arrFilter = Arr::filter($data, function ($val) use ($mod) {
            return $val % $mod;
        });

        $this->assertSame(
            \preg_replace('~\W~', '', $results),
            implode($arrFilter)
        );
    }

    public function testCache()
    {
        $code = <<<code
    {% for datum in data %}{{ datum }}{% endfor %}
code;

        $results = $this->eval($code);

        $this->assertSame(
            $results,
            $this->flow->render($this->folder . ':' . $this->lastView)
        );
    }

    /**
     * @expectedException \Bavix\Exceptions\Runtime
     */
    public function testEndFor()
    {
        $code = <<<code
    {% for datum in data %}
code;

        $this->eval($code);
    }

}
