Directive: include, partial
=========
 
variables
```php
$args = [
    'message' => 'Pleasant development'
]
```

template: app:tmp
```php
<h1>{{ message }}</h1>
```

### Directive {% include %}

Input:
```php
{% include 'app:tmp' %}
```

Output:
```html
<h1>Pleasant development</h1>
```

### Directive {% partial %}

It is not a typo of ".bxf".

Input:
```php
{% partial 'app:tmp.bxf' %}
```

Output:
```html
<h1>{{ message }}</h1>
```
