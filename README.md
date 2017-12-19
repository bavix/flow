# flow

## syntax

### set variable
Installation of variables.

```php
{% set begin = -10 %}
```

### Dump
Dumps information about a variable.

```php
{% dump(variable) %}
{% dump(user, profile) %}
```

### for / foreach 
Cycles.

```php
{% for item in items %}
    // todo
{% endfor %}
```

```php
{% for items as item %}
    // todo
{% endfor %}
```

```php
{% for items as key => item %}
    // todo
{% endfor %}
```

In a cycle there is a loop variable.
```php
{% for item in items %}
    {% dump(loop) %}
{% endfor %}
```

### literal
Everything that in the literal tag isn't processed.

```php
{% literal %}{! html !}{% endliteral %}
```
