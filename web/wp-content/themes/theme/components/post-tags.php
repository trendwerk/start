<?php
$tags = $p->tags();

if (!$tags) {
  return;
}
?>

<div data-name="post-tags">
  <p class="mb-2 font-bold text-gray-800"><?= __('Tags', 'theme'); ?></p>

  <div class="mb-6 flex flex-wrap gap-1.5">
    <?php foreach ($tags as $term) { ?>
      <a
        class="rounded bg-gray-100 px-3 py-1 text-gray-800 no-underline hover:bg-primary-600 hover:text-white"
        href="<?= $term->link(); ?>"
      >
        <?= $term->title(); ?>
      </a>
    <?php } ?>
  </div>
</div>
