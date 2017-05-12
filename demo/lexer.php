<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$lexer = new \Bavix\Flow\Lexer();

//var_dump($lexer->parse('Hello, {{ name }}!'));
//var_dump($lexer->parse('Hello, {{ &= low( name ) }}!'));
//var_dump($lexer->parse('{{extends }}{% literal %}Hello, {{&=low(name,\'hello\')}}!{% / literal %}'));

//$source = '{% for i in s ... 100%}{{i}} {{{i}}}{%/for%}';
//$source = '{% for i in $range%}{{i}} {{{i}}}{{/for}}';
//$source = '{% for i in range()%}{{i}} {{{i}}}{%/for%}';

//$source = '{{$a=trim($a)}}';
$source = '{% for task in tasks %}{{ task }}{%/for%}';

var_dump($source, $lexer->parse($source));

//layout.flow
