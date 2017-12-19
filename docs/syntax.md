Syntax
======

### Types

| Type | Example |
| :--- | :--- |
| callable | `{{ substr(hello, 0, 1) }}` |
| variable | `{{ hello }}` |
| number | `{{ 9501 }}`, `{{ 9501.03 }}` |
| string | `{{ 'hello' }}`, `{{ 'hello' ~ ' ' ~ 'world' }}` |
| object | `{{ obj.propery }}` |
| array | `{{ arr[0] }}`, `{{ arr['property'] }}` |

### Directives

### Operators

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

### Helpers

