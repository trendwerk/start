<?php
use Theme\Menu;

$menu = new Menu('main');
?>

<div
  class="
    menu top-0 left-0 z-10 flex flex-col
    max-lg:pointer-events-none max-lg:absolute max-lg:h-screen max-lg:w-full max-lg:-translate-y-full max-lg:overflow-auto max-lg:bg-primary-900 max-lg:p-5 max-lg:pt-20 max-lg:opacity-0 max-lg:transition-opacity
    lg:flex-row lg:flex-wrap lg:justify-end
  "
  data-name="menu"
>
  <?php foreach ($menu->items() as $item) { ?>
    <?= component('menu-item', [
      'item' => $item,
      'level' => 1,
    ]); ?>
  <?php } ?>
</div>
