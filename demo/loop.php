<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

//$data = [1, 2, 3, 4, 5];
$data = new ArrayObject();

foreach (range(1, 100) as $item)
{
    $data[\Bavix\Helpers\Str::random()] = $item;
}

$loop = new \Bavix\Flow\Loop($data);

foreach ($data as $key => $item)
{
    $loop->next($key);
    var_dump($loop);
}
