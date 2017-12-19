Directive {% block %}
=====================

Block type:
* append
* prepend

### Append
template: app:layout.bxf
```php
{% block content append %}If not extends{% endblock %}
```

### Prepend 

template: app:layout.bxf
```php
{% block content prepend %}If not extends{% endblock %}
```

### Default (reset)

template: app:layout.bxf
```php
{% block content %}If not extends{% endblock %}
```

template: app:content.bxf
```php
{% extends 'app:layout' %}

{% block content %}
    <h1>Hello World</h1>
{% endblock %}
```
