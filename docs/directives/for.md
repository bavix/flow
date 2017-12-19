Directive {% for %}
=====================
 
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

forelse

```php
{% for item in items %}
    {% dump(loop) %}
{% forelse %}
    <p>Items is empty</p>
{% endfor %}
```