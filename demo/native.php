<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new Bavix\FlowNative\Helper();
$native = new \Bavix\FlowNative\FlowNative($helper);

$helper->add('hello', function ($name) {
    return "Hello, {$name}";
});

$native->addFolder('app', __DIR__ . '/flow');

echo $native->render('app:welcome.php', [
    'engine' => 'Flow Native'
]);
