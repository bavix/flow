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
//$source = '{%for task in user.tasks()%}{{\'{{\'}} {%literal%}{!html!}{%endliteral%} {{ user.name() ?? \'help\' }} {%endfor%}';
//$source = '{% foreach tasks as task %}{{ task }}{%endforeach%}';

$source = '{% helper input( options ) %}{% with options %}
    <label id=\'#-{{ id(.name) }}\' >{{ .label }}</label>
    <input type="{{ .for }}" name="{{ options.name }}" value="{{ options.value }}" />
{% endwith %}{% endhelper %}

{% with user %}

{{
.name ~ .test ~

.obj ~ obj(.obj)

}}

{% endwith %}

{%for task in user.tasks()%}
    {{ @.iteration }}
    {{ @.index }}
    {{ @.key }}
    {{ .name ~ \' \\\' \\\' \' ~ .lastName  }}
{%endfor%}

<select>
    {% for i in 1 ... 20 %}
        {% if i % 2 %}
            {{ i++ }}
            {{ ++i }}
            {{ i-- }}
            {{ --i }}
            {{ i+=1 }}
            {{ i-=1 }}
            {{ i*=1 }}
            {{ i/=1 }}
            {{ i>>=1 }}
            {{ i<<=1 }}
        {% endif %}
    {% endfor %}
</select>

';

$source .= '{%literal%}' . $source . '{%endliteral%} {% open %}123123{% endopen %}';

foreach ($lexer->tokens($source) as $type => $types)
{
    if ($type === \Bavix\Flow\Lexer::LITERAL)
    {
        var_dump('literal', $types);
        continue;
    }

    foreach ($types as $_type)
    {
        var_dump($_type);
    }
}

//var_dump($source);

//layout.flow
