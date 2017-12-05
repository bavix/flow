<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new \Bavix\FlowNative\Helper();
$native = new \Bavix\FlowNative\FlowNative($helper);
$flow  = new \Bavix\Flow\Flow($native);

$native->addFolder('app', __DIR__ . '/app');

$lxm = new \Bavix\Flow\Lexem($flow);

//$data = $lxm->apply('for', 'for users as key => user');
$data = $lxm->apply('for', 'for (key, user) in users');

$rows = $data['rows'];
$row = $data['row'];
$key = $data['key'] ?? '$' . \Bavix\Helpers\Str::random();

echo <<<html
foreach ($rows as $key => $row):
    // ...foreach...
endforeach;
html;

