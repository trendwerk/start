<div data-name="related-posts">
  <?= component('latest-posts', [
    'title' => __('Related posts', 'theme'),
    'link' => $p->archiveLink(),
    'posts' => $p->related(),
  ]); ?>
</div>
