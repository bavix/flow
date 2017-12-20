Get Started
===========

[Get Started]
[[Documentation](./readme.md)]
[[Configuration](./configure.md)]

### Installation

The recommended way to install Flow is via Composer:
```bash
composer require "bavix/flow"
```

### Basic API Usage
This section gives you a brief introduction to the PHP API for Flow.

```php
$flow = new \Bavix\Flow\Flow();
$flow->native()->addFolder('app', __DIR__ . '/view');

echo $flow->render('app:layer', ['name' => 'Bavix']);
```
