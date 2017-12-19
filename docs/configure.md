Configuration
=============

#### compile
Here you specify the folder for compilation of a template.

```php
new \Bavix\Flow\Flow($native, [
    'compile' => '/path/to/compile'
]);
```

#### cache (PSR6)
We support PSR6. 
Caching allows to accelerate compilation of templates. 
Use intelligently.

```php
$pool = new \Stash\Pool();
$pool->setDriver(new \Stash\Driver\FileSystem([
    'path' => __DIR__ . '/cache'
]));

new \Bavix\Flow\Flow($native, [
    'cache' => $pool
]);
```

#### debug mode
___:warning: Slows down work of a shablonizator in tens of times. 
Use only when developing the project.

```php
new \Bavix\Flow\Flow($native, [
    'debug' => true
]);
```

<!--
#### minify / extends 
#### lexemes / directives
-->

```php
$native = new \Bavix\Flow\Native();
$flow   = new \Bavix\Flow\Flow($native, [
    'compile'    => __DIR__ . '/compile',
    'cache'      => $pool,
    'debug'      => true,
    'minify'     => true,
    'extends' => [
        \Bavix\Flow\Minify\Extensions\RemoveComments::class,
        \Bavix\Flow\Minify\Extensions\CollapseWhitespace::class
    ],
    'lexemes'    => [
        __DIR__ . '/lexemes',
    ],
    'directives' => [
        'll' => Demo\Directives\LlDirective::class
    ]
]);
```