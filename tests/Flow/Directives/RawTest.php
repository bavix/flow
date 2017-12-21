<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class RawTest extends Unit
{

    public function testRaw()
    {

        $code = <<<code
    {{ message }}
code;

        $raw = <<<code
    {! message !}
code;

        $message = __FUNCTION__;

        $this->assertSame(
            $this->eval($code, [
                'message' => $message
            ]),
            $this->eval($raw, [
                'message' => $message
            ])
        );

        $message = '<h1>' . $message;

        $this->assertSame(
            $this->eval($raw, [
                'message' => $message
            ]),
            $message
        );

        $this->assertSame(
            $this->eval($code, [
                'message' => $message
            ]),
            \htmlspecialchars($message, ENT_QUOTES)
        );

    }

}
