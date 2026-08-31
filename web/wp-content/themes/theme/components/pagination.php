<?php
global $wp_query;

$currentPage = get_query_var('paged') ?: 1;
$previousPage = $currentPage > 1 ? get_previous_posts_page_link() : null;
$nextPage = $currentPage < $wp_query->max_num_pages ? get_next_posts_page_link() : null;

if (!$previousPage && !$nextPage) {
  return;
}
?>

<div
  class="mt-10 flex w-full flex-wrap justify-between md:mt-15"
  data-name="pagination"
>
  <?php if ($previousPage) { ?>
    <?= component('button', [
      'title' => __('Previous page', 'theme'),
      'iconBefore' => component('svg/chevron-right', [
        'class' => 'rotate-180 transition-transform group-hover:-translate-x-1 -ml-1',
      ]),
      'iconAfter' => false,
      'url' => $previousPage,
    ]); ?>
  <?php } ?>

  <?php if ($nextPage) { ?>
    <?= component('button', [
      'class' => 'button ml-auto',
      'title' => __('Next page', 'theme'),
      'url' => $nextPage,
    ]); ?>
  <?php } ?>
</div>
