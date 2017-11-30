<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$lxm = new \Bavix\Flow\Lexem();

foreach ($lxm->data('for') as $regex)
{
//    var_dump($regex);
    if (preg_match($regex, 'for (a ? [1,2,3,4] : [4]) as kk => item', $outs))
    {
        var_dump($regex, $outs);
    }
}
