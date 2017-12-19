Directive {% for %}
=====================
 
Loops.

### Syntax IN (from vanilla js)

```php
{% for item in items %}
    // todo
{% endfor %}
```

```php
{% for (key, item) in items %}
    // todo
{% endfor %}
```

### Syntax AS (from php)

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

### Variable $loop

 * @property-read bool  $first
 * @property-read mixed $firstIndex
 * @property-read mixed $index
 * @property-read int   $iteration
 * @property-read mixed $key
 * @property-read bool  $last
 * @property-read mixed $lastIndex

In a for there is a loop variable.
```php
{% for item in items %}
    {% dump(loop) %}
    {% if loop.first %}
        // todo if First iteration
    {% endif %}
{% endfor %}
```

### Foreach else (Forelse)

When items can be empty and it is necessary to inform on it the user.

```php
{% for item in items %}
    {% dump(loop) %}
{% forelse %}
    <p>Items is empty</p>
{% endfor %}
```
