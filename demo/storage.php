<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$data = [
    'item' => [
        'hello' => 'type',
        'world' => 'hello world'
    ]
];

$storage = new \Bavix\Flow\Storage($data);

$storage->offsetSet(
    'item.hello',
    $storage->offsetGet('item.world')
);

$storage->offsetUnset('item.world');

var_dump(isset($storage['item.world']));
var_dump(isset($storage['item.hello']));

var_dump($storage);
