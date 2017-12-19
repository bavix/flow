Directive {% with %}
==================

And you programmed pascal in language?
Then this design has to you be familiar.

Code without "directive `with`":

```php
{{ user.login }}
{{ user.firstName }}
{{ user.lastName }}
{{ user.email }}
```

Code with "directive `with`":

```php
{% with user %}
    {{ .login }}
    {{ .firstName }}
    {{ .lastName }}
    {{ .email }}
{% endwith %}
```
