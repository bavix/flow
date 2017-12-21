<?php

namespace Tests\Flow\Directives;

use Tests\Unit;

class MinifyTest extends Unit
{

    public function testEnable()
    {

        $code = <<<code
    <h1>{{ message }}</h1>

    <span>25</span>

<code>{! code !}</code>
code;

        $flow = clone $this->flow;

        $class = new \ReflectionClass(\get_class($flow));
        $property = $class->getProperty('minify');
        $property->setAccessible(true);

        $property->setValue($flow, true);

        $data = [
            'message' => __CLASS__,
            'code' => \file_get_contents(__FILE__),
        ];

        $lenCode = \strlen($this->eval($code, $data));
        $lenMinify = \strlen($this->eval($code, $data, $flow));

        $this->assertTrue($lenCode > $lenMinify);
    }

    public function testCollapse()
    {

        $code = <<<code
    <h1>{{ message }}</h1>

    <span>25</span>
code;

        $flow = clone $this->flow;

        $class = new \ReflectionClass(\get_class($flow));
        $property = $class->getProperty('extends');
        $property->setAccessible(true);

        $property->setValue($flow, [
            \Bavix\Flow\Minify\Extensions\CollapseWhitespace::class
        ]);

        $data = [
            'message' => __CLASS__
        ];

        $minify = $this->eval($code, $data, $flow);
        $results= $this->eval($code, $data);

        $this->assertSame(
            $minify,
            \preg_replace('~[\s\n]+~', ' ', $results)
        );
    }

    public function testComments()
    {

        $code = <<<code
        <!-- print message -->
    <h1>{{ message }}</h1>
        <!-- /print message -->
    
        <!-- tag span -->
    <span>25</span>
    <!-- /tag span -->
code;

        $flow = clone $this->flow;

        $class = new \ReflectionClass(\get_class($flow));
        $property = $class->getProperty('extends');
        $property->setAccessible(true);

        $property->setValue($flow, [
            \Bavix\Flow\Minify\Extensions\RemoveComments::class
        ]);

        $data = [
            'message' => __CLASS__
        ];

        $results= $this->eval($code, $data);
        $minify = $this->eval($code, $data, $flow);

        $this->assertRegExp(
            '~<!--\X*-->~',
            $results
        );

        $this->assertNotRegExp(
            '~<!--\X*-->~',
            $minify
        );

    }

    public function testExtends()
    {

        $codeWithoutComments = <<<code
    <h1>{{ message }}</h1>
    
    
    
    <span>25</span>
code;

        $code = <<<code
        <!-- print message -->
    <h1>{{ message }}</h1>
        <!-- /print message -->
    
        <!-- tag span -->
    <span>25</span>
    <!-- /tag span -->
code;

        $flow = clone $this->flow;

        $class = new \ReflectionClass(\get_class($flow));
        $property = $class->getProperty('extends');
        $property->setAccessible(true);

        $property->setValue($flow, [
            \Bavix\Flow\Minify\Extensions\RemoveComments::class,
            \Bavix\Flow\Minify\Extensions\CollapseWhitespace::class
        ]);

        $data = [
            'message' => __CLASS__
        ];

        $minify = $this->eval($code, $data, $flow);
        $results= $this->eval($codeWithoutComments, $data);

        $this->assertSame(
            $minify,
            \preg_replace('~[\s\n]+~', ' ', $results)
        );
    }

}
