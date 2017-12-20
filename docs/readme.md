Documentation
=============

[[Get Started](./get-started.md)]
[Documentation]
[[Configuration](./configure.md)]

Syntax
======

## Types

| Type | Example |
| :--- | :--- |
| callable | `{{ substring(hello, 0, 1) }}` |
| variable | `{{ hello }}` |
| number | `{{ 9501 }}`, `{{ 9501.03 }}` |
| string | `{{ 'hello' }}`, `{{ 'hello' ~ ' ' ~ 'world' }}` |
| object | `{{ obj.propery }}` |
| array | `{{ arr[0] }}`, `{{ arr['property'] }}` |

## Directives

 * [block](./directives/block.md) - is used to define a named area of template source for template inheritance.
 * [dump](./directives/dump.md) - This function displays structured information about one or more expressions that includes its type and value. Arrays and objects are explored recursively with values indented to show structure.
 * [for](./directives/for.md) - is used to create simple loops.
 * [if](./directives/if.md) - is used to create simple conditions.
 * [include](./directives/include.md#directive-{%-include-%}) - is used to connections of other templates and their performance.
 * [partial](./directives/include.md#directive-{%-partial-%}) - is used to connections of other templates.
 * [literal](./directives/literal.md) - everything that in the literal tag isn't processed. 
 * [set](./directives/set.md) - installation of variables.
 * [with](./directives/with.md) - the language design created for lazy people.

## Helpers

#### Work with string type

* [substring](https://php.net/manual/en/function.substr.php) - Return part of a string.
* [capitalize](https://php.net/manual/en/function.ucwords.php) - Uppercase the first character of each word in a string.
* [upper](https://php.net/manual/en/function.strtoupper.php) - Make a string uppercase.
* [lower](https://php.net/manual/en/function.strtolower.php) - Make a string lowercase.
* [length](https://php.net/manual/en/function.strlen.php) - Get string length.
* shorten - We do the text shorter.
* split
* [ucFirst](https://php.net/manual/en/function.ucfirst.php) - Make a string's first character uppercase.
* [lcFirst](https://php.net/manual/en/function.lcfirst.php) -  Make a string's first character lowercase.
* random - Return of a random string.
* [uniqid](https://php.net/manual/en/function.uniqid.php) - Generate a unique ID
* fileSize
* translit
* first
* withoutFirst
* last
* withoutLast
* snakeCase
* camelCase
* friendlyUrl
* toNumber
* pos

#### Work with array type

* in
* range
* merge
* shuffle
* keyExists

#### Work with number type

* randomInt
* format

#### Work with json type

* jsonEncode
* jsonDecode

## Operators

#### Assignment operators 
Operators: `+`, `-`, `*`, `/`, `%`

| Example | Result |
| :--- | :--- |
| `{{ a + 95 }}` | `echo $a + 95` |
| `{{ a - 95 }}` | `echo $a - 95` |
| `{{ a * 95 }}` | `echo $a * 95` |
| `{{ a / 95 }}` | `echo $a / 95` |
| `{{ a % 95 }}` | `echo $a % 95` |

#### Logical operators
Operators: `||`, `&&`, `^`, `!`, `and`, `or`, `xor`

| Example | Result |
| :--- | :--- |
| `{% if a ∣∣ b %}` | `if ($a ∣∣ $b)` |
| `{% if a && b %}` | `if ($a && $b)` |
| `{% if a ^ b %}` | `if ($a ^ $b)` |
| `{% if !b %}` | `if (!$b)` |
| `{% if a and b %}` | `if ($a and $b)` |
| `{% if a or b %}` | `if ($a or $b)` |
| `{% if a xor b %}` | `if ($a xor $b)` |

#### Comparison operators
Operators: `>`, `>=`, `<`, `<=`, `==`, `!=`, `===`, `!==`

| Example | Result |
| :--- | :--- |
| `{% if a > b %}` | `if ($a > $b)` |
| `{% if a === b %}` | `if ($a === $b)` |
| `{% if a <= b %}` | `if ($a <= $b)` |

#### Bitwise operators
Operators: `|`, `&`, `^`, `>>`, `<<`

| Example | Result |
| :--- | :--- |
| `{{ a & b }}` | `echo $a & $b` |
| `{{ a ^ b }}` | `echo $a ^ $b` |
| `{{ a << b }}` | `echo $a << $b` |

#### Assignment operators 
Operators: `=`, `+=`, `-=`, `*=`, `/=`, `%=`, `&=`, `|=`, `^=`, `>>=`, `<<=`

| Example | Result |
| :--- | :--- |
| `{% set a += 1 %}` | `$a += 1` |

#### String concatenation operators 
Operators: `~`

| Example | Result |
| :--- | :--- |
| `{{ 'a' ~ 'b' }}` | `echo 'a' . 'b'` |
| `{{ hello ~ 'b' }}` | `echo $hello . 'ab'` |
| `{{ a ~ 9501 ~ '&' }}` | `echo $a . 9501 . '&'` |

#### Ternary operators 
Operators: `?:`

| Example | Result |
| :--- | :--- |
| `{{ a ?: b }}` | `echo $a ?: $b` |
| `{{ a ? c : b }}` | `echo $a ? $c : $b` |
