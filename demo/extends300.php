<?php

include_once __DIR__ . '/boostrap.php';

$native = new \Bavix\Flow\Native();

$pool = new \Stash\Pool();
$pool->setDriver(new \Stash\Driver\FileSystem([
    'path' => __DIR__ . '/cache'
]));

$flow = new \Bavix\Flow\Flow($native, [
    'compile' => __DIR__ . '/compile',
    'cache'   => $pool,
    'debug'   => false
]);

$native->addFolder('extends300', __DIR__ . '/view/extends300');

echo $flow->render('extends300:start');
