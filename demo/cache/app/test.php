<?php $this->ext->blocks()->extends($this->flow->path('app:layout'))?>

<?php $this->ext->blocks()->start('hello', 'reset')?>
    <?php \Bavix\Flow\Directives\WithDirective::push($user)?>
        <?php if (!empty(\Bavix\Flow\Directives\WithDirective::last()->last)):?>
            <h1>Hello, <?php echo \htmlentities(\Bavix\Flow\Directives\WithDirective::last()->login); ?></h1>
            <h3><?php echo \htmlentities(\Bavix\Flow\Directives\WithDirective::last()->last.' '.\Bavix\Flow\Directives\WithDirective::last()->first); ?></h3>
            <?php $_wSfltt5XKfJHoPXN=\Bavix\Flow\Directives\WithDirective::last()['images']();\Bavix\Flow\Directives\ForDirective::loop('$_Gr9aqadv87kajYLq', $_wSfltt5XKfJHoPXN);foreach ($_wSfltt5XKfJHoPXN as $_tdNtfph8VEGU6SHo => $image): $loop = \Bavix\Flow\Directives\ForDirective::loop('$_Gr9aqadv87kajYLq');$loop->next($_tdNtfph8VEGU6SHo);?>
                <?php var_dump($image); ?>
                <?php \Bavix\Flow\Directives\WithDirective::push($image)?>
                    <img src="<?php echo \Bavix\Flow\Directives\WithDirective::last()->path; ?>" />
                <?php \Bavix\Flow\Directives\WithDirective::pop()?>
            <?php unset($loop); endforeach; ?>
            <?php echo $help; ?>
        <?php endif; ?>
    <?php \Bavix\Flow\Directives\WithDirective::pop()?>
<?php echo $this->ext->blocks()->end()?>
