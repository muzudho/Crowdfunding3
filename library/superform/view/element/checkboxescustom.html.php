<?php foreach ($this['options'] as $key => $checkbox): ?>

<?php if(empty($checkbox['category'])) : ?>
<?php if($key != 0) : ?></ul><?php endif;?>
<p><?php echo $checkbox->label;?></p>
<ul<? if (PC_VIEW): ?> class="heightLineParent"<? endif; ?>>
<?php else : ?>
    <li><?php echo $checkbox->getInnerHTML() ?></li>
<?php endif; ?>
<?php endforeach ?>
</ul>