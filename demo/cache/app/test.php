<?php $this->ext->blocks()->extends($this->flow->path('app:layout'))?>

<?php $this->ext->blocks()->start('hello', 'reset')?>
    <?php \Bavix\Flow\Directives\WithDirective::push($user)?>
        <?php if (!empty(\Bavix\Flow\Directives\WithDirective::last()->last)):?>
            <h1>Hello, <?php echo \htmlentities(\Bavix\Flow\Directives\WithDirective::last()->login); ?></h1>
            <h3><?php echo \htmlentities(\Bavix\Flow\Directives\WithDirective::last()->last.' '.\Bavix\Flow\Directives\WithDirective::last()->first); ?></h3>
            <?php $_cVCdZPpxuay5rYw1=\Bavix\Flow\Directives\WithDirective::last()['images']();\Bavix\Flow\Directives\ForDirective::loop('$_ScuFfqh5AZfbc5Fw', $_cVCdZPpxuay5rYw1);foreach ($_cVCdZPpxuay5rYw1 as $_1fdkNJkvpiLKpa01 => $image): $loop = \Bavix\Flow\Directives\ForDirective::loop('$_ScuFfqh5AZfbc5Fw');$loop->next($_1fdkNJkvpiLKpa01);?>
                <?php var_dump($image); ?>
                <?php \Bavix\Flow\Directives\WithDirective::push($image)?>
                    <img src="<?php echo \Bavix\Flow\Directives\WithDirective::last()->path; ?>" />
                <?php \Bavix\Flow\Directives\WithDirective::pop()?>
            <?php unset($loop); endforeach; ?>
            <?php echo $help; ?>
        <?php endif; ?>
    <?php \Bavix\Flow\Directives\WithDirective::pop()?>
<?php echo $this->ext->blocks()->end()?>
