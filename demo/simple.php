<?php

include_once __DIR__ . '/boostrap.php';

$native = new \Bavix\Flow\Native();
$flow  = new \Bavix\Flow\Flow($native, [
    'cache' => __DIR__ . '/cache',
    'debug' => true,
    'directives' => [
        'json' => \Demo\Directives\JsonDirective::class
    ]
]);

$flow->lexem()->addFolder(__DIR__ . '/lexemes');

$native->addFolder('app', __DIR__ . '/view/app');

$args = [
    'help' => __FILE__,
];

if (1 === \Bavix\Helpers\Num::randomInt(0, 1))
{
    $args['items'] = (function () {
        foreach (\range(1, 100) as $item)
        {
            yield $item;
        }
    })();
}

echo $flow->render('app:simple', $args);
