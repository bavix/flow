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

    public function testNext()
    {
        for ($i = 0; $i < \count($this->data) - 1; ++$i)
        {
            $this->loop->next(\key($this->data));
            next($this->data);

            $this->assertSame(
                $this->loop->index,
                $i
            );

            $this->assertSame(
                $this->loop->iteration,
                $i + 1
            );
        }
    }

    /**
     * @expectedException \Bavix\Exceptions\Invalid
     */
    public function testInvalid()
    {
        $this->loop->dada = 12;
    }

    public function testExists()
    {
        $this->assertTrue(
            isset($this->loop->firstIndex)
        );

        $this->assertFalse(
            isset($this->loop->nonoonono)
        );
    }



}
