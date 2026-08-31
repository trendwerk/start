<?php

namespace Theme\Taxonomies;

use Theme\PostTypes\Post;
use Theme\Traits\IsTerm;

class Category
{
  use IsTerm;

  public static string $taxonomy = 'category';

  /**
   * Get the latest posts from this category.
   */
  public function posts(int $number = 10): array
  {
    $ids = get_posts([
      'fields' => 'ids',
      'post_type' => Post::$postType,
      'posts_per_page' => $number,
      'tax_query' => [
        [
          'field' => 'term_id',
          'taxonomy' => static::$taxonomy,
          'terms' => $this->id(),
        ],
      ],
    ]);

    return array_map(function ($id) {
      return new Post($id);
    }, $ids);
  }
}
