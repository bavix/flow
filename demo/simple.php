<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new \Bavix\FlowNative\Helper();
$native = new \Bavix\Flow\Native($helper);
$flow  = new \Bavix\Flow\Flow($native, [
    'cache' => __DIR__ . '/cache',
    'debug' => true
]);

$native->addFolder('app', __DIR__ . '/view/app');

$helper->add('substr', '\mb_substr');
$helper->add('range', '\range');

$args = [
    'help' => __FILE__,
];

if (\random_int(0, 1))
{
    $args['items'] = (function () {
        foreach (\range(1, 100) as $item)
        {
            yield $item;
        }
    })();
}

echo $flow->render('app:simple', $args);
