<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new \Bavix\FlowNative\Helper();
$native = new \Bavix\Flow\Native($helper);
$flow  = new \Bavix\Flow\Flow($native, [
    'cache' => __DIR__ . '/cache',
    'debug' => true
]);

$native->addFolder('app', __DIR__ . '/app');

$helper->add('substr', '\mb_substr');
$helper->add('range', '\range');

echo $flow->render('app:simple', [
    'help' => __FILE__
]);
