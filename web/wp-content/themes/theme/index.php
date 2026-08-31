<?php
if (!defined('ABSPATH')) {
  exit;
}

use Theme\PostTypes\Page;
use Theme\PostTypes\Post;

$p = new Page(get_option('page_for_posts'));

// Title
if (is_author()) {
  $label = __('Articles by', 'theme');
  $author = get_the_author();
  $title = "{$label} {$author}";
} elseif (is_category() || is_tag() || is_tax()) {
  $term = get_queried_object();

  if ($term && isset($term->name)) {
    $label = __('Articles about', 'theme');
    $title = "{$label} &ldquo;{$term->name}&rdquo;";
  }
} else {
  $title = $p->title();
}

// Posts
global $wp_query;

$posts = array_map(function ($post) {
  return new Post($post->ID);
}, $wp_query->posts);
?>

<?= component('head'); ?>
<?= component('header'); ?>

<?= component('hero', [
  'title' => $title,
  'p' => $p,
]); ?>

<main class="section">
  <div class="wrapper">
    <h1><?= $title; ?></h1>
    <?= component('posts', ['posts' => $posts]); ?>
    <?= component('pagination'); ?>
  </div>
</main>

<?= component('cta', ['p' => $p]); ?>
<?= component('footer'); ?>
