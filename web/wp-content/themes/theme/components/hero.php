<header
  class="wrapper"
  data-name="hero"
>
  <?php if (isset($p) && $image = $p->image()) { ?>
    <?= component('image', [
      'class' => implode(' ', [
        'rounded-lg',
        is_front_page() ? 'max-h-[600px]' : 'max-h-[400px]',
      ]),
      'height' => 900,
      'loading' => 'eager',
      'src' => $image,
      'width' => 1200,
      'widths' => [335, 480, 600, 1200],
    ]); ?>
  <?php } else { ?>
    <hr />
  <?php } ?>
</header>
