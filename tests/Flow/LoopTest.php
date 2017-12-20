<?php

namespace Tests\Flow;

use Bavix\Flow\Loop;
use Tests\Unit;

class LoopTest extends Unit
{

    /**
     * @var array
     */
    protected $data = [1, 2, 3];

    /**
     * @var Loop
     */
    protected $loop;

    public function setUp()
    {
        parent::setUp();

        $this->loop = new Loop($this->data);
    }

    public function testFirst()
    {
        $this->assertSame(
            $this->loop->key,
            $this->loop->firstIndex
        );

        $this->assertSame(
            \key($this->data),
            $this->loop->key
        );

        \end($this->data);
        $this->assertSame(
            \key($this->data),
            $this->loop->lastIndex
        );
    }

}
