<?php if ($meta = $p->meta(
  $showAuthor ?? true,
  $showDate ?? true,
  $showCategories ?? true
)) { ?>
  <p
    class="font-semibold text-gray-500 <?= $class ?? ''; ?>"
    data-name="post-meta"
  >
    <?= $meta; ?>
  </p>
<?php } ?>
