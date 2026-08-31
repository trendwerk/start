<?php
use Theme\Menu;

$menu = new Menu('footer');
?>

<footer
  class="
    wrapper py-6 flex items-center gap-2 text-center flex-col
    lg:py-8 lg:flex-row lg:justify-between
  "
  data-name="footer"
>
  <div class="px-4 py-1">
    &copy; <?= date('Y'); ?> - <?= get_bloginfo('name'); ?>
  </div>

  <div class="flex flex-wrap justify-center items-end">
    <?php foreach ($menu->items() as $item) { ?>
      <a
        class="px-4 py-1 font-semibold no-underline text-gray-800 hover:text-primary-600"
        href="<?= $item->url; ?>"
        target="<?= $item->target; ?>"
      >
        <?= $item->title; ?>
      </a>
    <?php } ?>
  </div>
</footer>

<?php wp_footer(); ?>

</body>

</html>
