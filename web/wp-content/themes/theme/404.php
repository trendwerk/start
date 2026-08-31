<?= component('head'); ?>
<?= component('header'); ?>
<?= component('hero'); ?>

<main class="section">
  <div class="wrapper max-w-content">
    <h1>
      <?= __('Page not found', 'theme'); ?>
    </h1>

    <p>
      <?= __(
        'Unfortunately, this page does not exist. You can go to the homepage, or use the menu above to navigate to other pages.',
        'theme'
      ); ?>
    </p>

    <?= component('button', [
      'title' => __('To the homepage', 'theme'),
      'url' => home_url(),
    ]); ?>
  </div>
</main>

<?= component('cta'); ?>
<?= component('footer'); ?>
