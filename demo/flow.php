<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new \Bavix\FlowNative\Helper();
$native = new \Bavix\Flow\Native($helper);
$flow  = new \Bavix\Flow\Flow($native);

$native->addFolder('app', __DIR__ . '/app');

echo $flow->compile('app:test');
