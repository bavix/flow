<?php

namespace Tests\Flow\Directives;

use Bavix\Helpers\Arr;
use Stash\Driver\FileSystem;
use Stash\Pool;
use Tests\Unit;

class CacheTest extends Unit
{

    public function configure()
    {
        $pool = new Pool();
        $pool->setDriver(new FileSystem());

        return Arr::merge(parent::configure(), [
            'cache' => $pool
        ]);
    }

    public function testExecute()
    {
        $code = <<<code
    {{ bar }} {{ foo }} 
    {{ bar }} {{ foo }} 
    {{ bar }} {{ foo }} 
code;

        $code .= '{{ ' . (int)microtime(true) . ' }}';

        $data = [
            'foo' => 'bar',
            'bar' => 'foo',
        ];

        $this->assertSame(
            $this->eval($code, $data),
            $this->flow->render($this->folder . ':' . $this->lastView, $data)
        );
    }

    public function testExecute2()
    {
        $this->testExecute();
    }

}
