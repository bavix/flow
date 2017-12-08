<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$native = new \Bavix\Flow\Native();

$native->addFolder('r20', __DIR__ . '/view/r20');

$flow = new \Bavix\Flow\Flow($native, [
    'cache' => __DIR__ . '/cache',
    'debug' => true
]);

echo $flow->render('r20:layout');
