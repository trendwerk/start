<?php if ($posts) { ?>
  <section
    class="section full-width"
    data-name="latest-posts"
  >
    <div class="wrapper">
      <div class="flex flex-wrap justify-between items-end mb-10 md:mb-15">
        <h2 class="h1 md:text-4xl/tight mb-0">
          <?= $title; ?>
        </h2>

        <?= component('button', [
          'class' => 'button hidden sm:flex',
          'title' => __('View all articles', 'theme'),
          'url' => $link,
        ]); ?>
      </div>

      <?= component('posts', [
        'posts' => $posts,
        'postClass' => 'nth-3:sm:hidden nth-3:lg:block',
      ]); ?>
    </div>
  </section>
<?php } ?>
