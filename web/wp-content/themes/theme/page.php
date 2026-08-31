<?php
use Theme\PostTypes\Page;

$p = new Page(get_the_ID());
?>

<?= component('head'); ?>
<?= component('header'); ?>
<?= component('hero', ['p' => $p]); ?>

<main class="section">
  <div class="wrapper max-w-content">
    <h1>
      <?= $p->title(); ?>
    </h1>

    <?= $p->content(); ?>
  </div>
</main>

<?= component('cta', ['p' => $p]); ?>
<?= component('footer'); ?>

