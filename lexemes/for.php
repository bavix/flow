<?php return array (
  'closed' => true,
  'properties' => 
  array (
    'rows' => 
    array (
      'types' => 
      array (
        0 => 'variable',
        1 => 'callable',
        2 => 'array',
        3 => 'ternary',
      ),
    ),
  ),
  'syntax' => 
  array (
    0 => '\\({{ row }} in {{ rows }}\\)',
    1 => '{{ row }} in {{ rows }}',
    2 => '\\({{ rows }} as {{ row }}\\)',
    3 => '{{ rows }} as {{ row }}',
    4 => '\\({{ rows }} as {{ key }} => {{ row }}\\)',
    5 => '{{ rows }} as {{ key }} => {{ row }}',
    6 => '\\(\\({{ key }}, {{ row }}\\) in {{ rows }}\\)',
    7 => '\\({{ key }}, {{ row }}\\) in {{ rows }}',
  ),
  'directives' => 
  array (
    'continue' => 
    array (
      'syntax' => 
      array (
        0 => '({{ num }})?',
      ),
    ),
    'break' => 
    array (
      'syntax' => 
      array (
        0 => '({{ num }})?',
      ),
    ),
    'forelse' => NULL,
  ),
);