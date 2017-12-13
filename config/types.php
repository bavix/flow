<?php

return [
    'callable' => '[.\w\s(,\'")]+',
    'variable' => '[\w\'":.\[\]()\s]+',
    'array'    => '(array\(|\[)[\s\S]*(\]|\))',
    'ternary'  => '\X+\?\X*:\X+',
    'bool'     => '\s*(true|false)\s*',
    'any'      => '\X+',
];
