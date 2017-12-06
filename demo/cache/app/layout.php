<?php echo \htmlentities($help); ?>
<?php echo $help; ?>

{! help !}

<?php $this->ext->blocks()->start('hello', 'reset')?>
    <?php echo \file_get_contents($this->native->path('app:test'.'.bxf'))?>
<?php echo $this->ext->blocks()->end()?>
