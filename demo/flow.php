<?php

include_once __DIR__ . '/boostrap.php';

$native = new \Bavix\Flow\Native();
$flow  = new \Bavix\Flow\Flow($native, [
    'cache' => __DIR__ . '/cache',
    'debug' => true,
    'minify' => true,
    'directives' => [
        'll' => Demo\Directives\LlDirective::class
    ]
]);

$pool = new \Stash\Pool();
$pool->setDriver(new \Stash\Driver\FileSystem([
    'path' => __DIR__ . '/cache/__stash__'
]));

$lexem = $flow->lexem();
$lexem->setPool($pool);
$lexem->addFolder(__DIR__ . '/lexemes');

$native->addFolder('bar', __DIR__ . '/view/bar');
$native->addFolder('foo', __DIR__ . '/view/foo');

class User implements ArrayAccess
{
    protected $data;
    use \Bavix\Iterator\Traits\ArrayAccess;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        // todo
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

}

class Image
{
    public $path;
    public function __construct($path)
    {
        $this->path = $path;
    }
}

$args = [
    'help' => '<h1>Help me!</h1>',
    'user' => new User([
        'last' => 'Babichev',
        'first' => 'Maxim',
        'login' => 'rez1dent3',
        'images' => function () {
            return [
                new Image('http://via.placeholder.com/350x150'),
                new Image('http://via.placeholder.com/350x150')
            ];
        },
        'cars' => function () {
            return [];
        }
    ]),
    'menu' => [
        '<a href="/flow.php">Home</a>',
        '<a href="/simple.php">Simple</a>',
    ]
];

//var_dump($args['user']->images());die;

echo $flow->render('foo:test', $args);
