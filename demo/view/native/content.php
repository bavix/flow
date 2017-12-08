<!DOCTYPE HTML>
<html>
<head>
    <title>Hello world</title>
</head>
<body>
<h1><?php $this->ext->blocks()->start('h1');
    echo $this->ext->blocks()->end() ?></h1>
<?php $this->ext->blocks()->start('content');
echo $this->ext->blocks()->end() ?>
</body>
</html>
