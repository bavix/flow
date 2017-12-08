<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$native = new \Bavix\Flow\Native();

$native->addFolder('app', __DIR__ . '/view/native');

echo $native->render('app:welcome.php', [
    'engine' => 'Flow Native'
]);
