<?php return array (
  'properties' => 
  array (
    'path' => 
    array (
      'types' => 
      array (
        0 => 'variable',
        1 => 'ternary',
      ),
    ),
    'options' => 
    array (
      'types' => 
      array (
        0 => 'variable',
        1 => 'callable',
        2 => 'array',
      ),
    ),
    'sandbox' => 
    array (
      'types' => 
      array (
        0 => 'bool',
      ),
    ),
  ),
  'syntax' => 
  array (
    0 => '\\({{ path }}, {{ sandbox }}, {{ options }}\\)',
    1 => '\\({{ path }}, {{ options }}\\)',
    2 => '\\({{ path }}\\)',
    3 => '{{ path }}',
  ),
);