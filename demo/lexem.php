<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$lxm = new \Bavix\Flow\Lexem();

foreach ($lxm->data('for') as $data)
{
//    var_dump($regex);
    if (preg_match($data['regexp'], 'for i in 1 ... 20', $outs))
    {
        var_dump($data, $outs, $lxm->closed('for'));
    }
}
