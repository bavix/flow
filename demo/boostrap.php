<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

\spl_autoload_register(function ($class) {
    if (0 === \Bavix\Helpers\Str::pos($class, 'Demo\\Directives\\'))
    {
        $ns    = explode('\\', $class);
        $class = array_pop($ns);

        require __DIR__ . '/directives/' . $class . '.php';
    }
});
