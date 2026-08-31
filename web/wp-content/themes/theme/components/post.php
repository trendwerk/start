<div
  class="<?= $class ?? ''; ?>"
  data-name="post"
>
  <a
    aria-label="<?= $p->title(); ?>"
    class="mb-6 block aspect-[3/2] w-full overflow-hidden rounded-lg bg-gray-600 hover:bg-gray-800"
    href="<?= $p->link(); ?>"
  >
    <?php if ($image = $p->image()) { ?>
      <?= component('image', [
        'class' => 'w-full h-full object-cover hover:scale-110 transition-transform duration-300',
        'height' => 240,
        'src' => $image,
        'width' => 360,
        'widths' => [335, 360],
      ]); ?>
    <?php } ?>
  </a>

  <h2 class="mb-2">
    <a href="<?= $p->link(); ?>">
      <?= $p->title(); ?>
    </a>
  </h2>

  <?= component('post-meta', [
    'p' => $p,
    'showAuthor' => false,
    'showCategories' => false,
  ]); ?>

  <p><?= $p->excerpt(); ?></p>

  <a href="<?= $p->link(); ?>">
    <?= __('Read more', 'theme'); ?>
  </a>
</div>
