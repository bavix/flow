Configuration
=============

[[Get Started](./get-started.md)]
[[Documentation](./readme.md)]
[Configure]

#### compile
Here you specify the folder for compilation of a template.

```php
new \Bavix\Flow\Flow($native, [
    'compile' => '/path/to/compile'
]);
```

#### folders

To this configuration it was necessary to write the following code:

```php
$flow   = new \Bavix\Flow\Flow();
$native = $flow->native;

$native->addFolder('account', __DIR__ . '/view/account');
$native->addFolder('posts', __DIR__ . '/view/posts');
$native->addFolder('about', __DIR__ . '/view/about');
$native->addFolder('dev', __DIR__ . '/view/dev');
$native->addFolder('jobs', __DIR__ . '/view/jobs');
```

After addition of this configuration, it became simpler to add the folder.

```php
new \Bavix\Flow\Flow($native, [
    'folders' => [
        'account'   => __DIR__ . '/view/account',
        'posts'     => __DIR__ . '/view/account',
        'about'     => __DIR__ . '/view/posts',
        'dev'       => __DIR__ . '/view/dev',
        'jobs'      => __DIR__ . '/view/jobs',
    ]
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
:warning: Slows down work of a shablonizator in tens of times. 
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
