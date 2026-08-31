<?php

namespace Theme\PostTypes;

use Theme\Taxonomies\Category;
use Theme\Taxonomies\Tag;
use Theme\Traits\HasFields;
use Theme\Traits\IsPost;

class Post
{
  use IsPost;
  use HasFields;

  public static string $postType = 'post';
  public static array $labels;

  public static function init(): void
  {
    static::$labels = [
      'name' => __('Posts', 'theme'),
      'singular_name' => __('Post', 'theme'),
    ];

    // Register post fields
    add_action('acf/init', [static::class, 'registerFields']);
  }

  /**
   * Register post fields.
   */
  public static function registerFields()
  {
    $key = static::$postType;

    acf_add_local_field_group([
      'key' => "{$key}_settings",
      'title' =>
        static::$labels['singular_name'] . ' ' . __('settings', 'theme'),
      'fields' => [
        [
          'key' => "field_{$key}_thumbnail",
          'name' => '_thumbnail_id',
          'label' => __('Image', 'theme'),
          'type' => 'image',
          'return_format' => 'id',
          'min_width' => 1200,
          'min_height' => 900,
        ],
      ],
      'instruction_placement' => 'field',
      'location' => [
        [
          [
            'param' => 'post_type',
            'operator' => '==',
            'value' => $key,
          ],
        ],
      ],
    ]);
  }

  /**
   * Return the post author, date and categories as a string.
   */
  public function meta(
    bool $showAuthor = true,
    bool $showDate = true,
    bool $showCategories = true
  ): ?string {
    $meta = [];

    // Author
    if ($showAuthor) {
      $label = __('By', 'theme');
      $author = $this->author();
      $name = $author->name();
      $link = $author->link();
      $class =
        'text-gray-800 no-underline hover:text-primary-600 hover:underline';

      $meta[] = "$label <a class='{$class}' href='{$link}'>{$name}</a>";
    }

    // Date
    if ($showDate) {
      $meta[] = $this->date();
    }

    // Categories
    if ($showCategories && ($categories = $this->categories())) {
      $label =
        count($categories) == 1
          ? __('category', 'theme')
          : __('categories', 'theme');

      $categories = array_map(function ($term) {
        $class =
          'text-gray-800 no-underline hover:text-primary-600 hover:underline';
        $link = $term->link();
        $title = $term->title();

        return "<a class='{$class}' href='{$link}'>{$title}</a>";
      }, $categories);

      $categories = implode(', ', $categories);

      $meta[] = "{$label}: {$categories}";
    }

    if (empty($meta)) {
      return null;
    }

    $meta = implode(', ', $meta);

    return $meta;
  }

  /**
   * Return related posts of the current post, based on matching tags or categories.
   */
  public function related(int $number = 3): array
  {
    $posts = [];
    $query = [
      'fields' => 'ids',
      'post_type' => static::$postType,
      'posts_per_page' => $number,
      'post__not_in' => [$this->id],
    ];

    // Get related posts by tag
    if ($terms = $this->tags()) {
      $termIds = array_map(function ($term) {
        return $term->id();
      }, $terms);

      $relatedPosts = get_posts(
        array_merge($query, [
          'tax_query' => [
            [
              'field' => 'term_id',
              'taxonomy' => Tag::$taxonomy,
              'terms' => $termIds,
            ],
          ],
        ])
      );

      $posts = array_merge($posts, $relatedPosts);
    }

    // Add related posts by category if we don't have enough posts
    if (count($posts) < $number && ($terms = $this->categories())) {
      $termIds = array_map(function ($term) {
        return $term->id();
      }, $terms);

      $relatedPosts = get_posts(
        array_merge($query, [
          'post__not_in' => array_merge([$this->id], $posts),
          'tax_query' => [
            [
              'field' => 'term_id',
              'taxonomy' => Category::$taxonomy,
              'terms' => $termIds,
            ],
          ],
        ])
      );

      $posts = array_merge($posts, $relatedPosts);
    }

    // Add latest posts if we don't have enough posts
    if (count($posts) < $number) {
      $latestPosts = get_posts(
        array_merge($query, [
          'post__not_in' => array_merge([$this->id], $posts),
        ])
      );

      $posts = array_merge($posts, $latestPosts);
    }

    return array_map(function ($id) {
      return new self($id);
    }, $posts);
  }
}
