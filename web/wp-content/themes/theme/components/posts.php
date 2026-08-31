<div data-name="posts">
  <?php if ($posts) { ?>
    <div class="grid gap-10 sm:grid-cols-2 md:gap-15 lg:grid-cols-3">
      <?php foreach ($posts as $p) { ?>
        <?= component('post', [
          'p' => $p,
          'class' => $postClass ?? null,
        ]); ?>
      <?php } ?>
    </div>
  <?php } else { ?>
    <p>
      <?= __('No articles were found', 'theme'); ?>.
    </p>
  <?php } ?>
</div>
