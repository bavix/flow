<?php

namespace Tests\Flow\Directives;

use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;
use Tests\Unit;

class DumpTest extends Unit
{

    /**
     * @outputBuffering
     */
    public function testDump()
    {
        $code = '{% dump a %}';
        $a = 'hello';

        ob_start();
        var_dump($a);
        $dump = ob_get_clean();

        $this->assertSame(
            \substr($this->eval($code, ['a' => $a]), -\strlen($a)),
            \substr(trim($dump), -\strlen($a))
        );
    }

}
