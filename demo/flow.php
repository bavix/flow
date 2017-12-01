<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new \Bavix\FlowNative\Helper();
$native = new \Bavix\FlowNative\FlowNative($helper);
$flow  = new \Bavix\Flow\Flow($native);

$native->addFolder('app', __DIR__ . '/app');

foreach ($flow->compile('app:layout') as $data)
{
    foreach ($data as $datum)
    {
        //echo \Bavix\Helpers\JSON::encode(
        var_dump(
            $datum
        );
    }
}
