<a
  class="group <?= $class ?? 'button'; ?>"
  data-name="button"
  <?php
  if (!empty($url)) {
    echo 'href="' . $url . '" ';
  }

  if (!empty($target) && $target === '_blank') {
    echo 'rel="nofollow" ';
  }

  if (!empty($target)) {
    echo 'target="' . $target . '" ';
  }
  ?>
>
  <?= $iconBefore ?? null; ?>

  <?= $title; ?>

  <?= $iconAfter ?? component('svg/chevron-right', [
    'class' => 'transition-transform group-hover:translate-x-1 -mr-1',
  ]); ?>
</a>
