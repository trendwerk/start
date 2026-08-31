<?php
if (empty($buttons)) {
  return;
}
?>

<div
  class="<?= $class ?? ''; ?> flex flex-wrap gap-2"
  data-name="buttons"
>
  <?php foreach ($buttons as $button) { ?>
    <?= component('button', array_merge([
      'class' => $button['style'] == 'blue' ? 'button' : "button-{$button['style']}",
    ], $button['link'])); ?>
  <?php } ?>
</div>
