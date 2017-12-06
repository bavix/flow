<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new \Bavix\FlowNative\Helper();
$native = new \Bavix\Flow\Native($helper);
$flow  = new \Bavix\Flow\Flow($native);

$native->addFolder('app', __DIR__ . '/app');

$lxm = new \Bavix\Flow\Lexem($flow);

//$data = $lxm->apply('for', 'for users as key => user');
$data = $lxm->apply('for', 'for image in .images');

$rows = $data['rows'];
$row = $data['row'];
$key = $data['key'] ?? '$' . \Bavix\Helpers\Str::random();

echo <<<html
foreach ($rows as $key => $row):
    // ...foreach...
endforeach;
html;

