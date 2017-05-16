<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$lexer = new \Bavix\Flow\Lexer();

//var_dump($lexer->tokens('Hello, {{ name }}!'));
//var_dump($lexer->tokens('Hello, {{ &= low( name ) }}!'));
//var_dump($lexer->tokens('{{extends }}{% literal %}Hello, {{&=low(name,\'hello\')}}!{% endliteral %}'));

//$source = '{% for i in s ... 100%}{{i}} {{{i}}}{%endfor%}';
//$source = '{% for i in $range%}{{i}} {{{i}}}{{endfor}}';
//$source = '{% for i in range()%}{{i}} {{{i}}}{%endfor%}';

//$source = '{{$a=trim($a)}}';
//$source = '{% for task in tasks %}{{ task }}{%endfor%}';
$source = '{%for task in user.tasks()%}{{\'{{\'}} {%literal%}{!html!}{%endliteral%} {{ a = [1, 2, 3] }} {%endfor%}';
//$source = '{% foreach tasks as task %}{{ task }}{%endforeach%}';

foreach ($lexer->tokens($source) as $type => $types)
{
    if ($type === \Bavix\Flow\Lexer::LITERAL)
    {
        var_dump($types);
        continue;
    }

    foreach ($types as $_type)
    {
        var_dump($_type);
    }
}

var_dump($source);

//layout.flow
