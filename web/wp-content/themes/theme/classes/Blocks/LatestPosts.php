<?php

namespace Theme\Blocks;

use Theme\PostTypes\Post;
use Theme\Taxonomies\Category;

class LatestPosts
{
  protected static $name = 'latest-posts';
  protected static $title;

  public static function init()
  {
    add_action('acf/init', [static::class, 'register']);
    add_action('acf/init', [static::class, 'registerFields']);
  }

  public static function register()
  {
    static::$title = __('Latest articles', 'theme');

    acf_register_block_type([
      'name' => static::$name,
      'title' => static::$title,
      'description' => __(
        'Three latest articles, can be filtered by category',
        'theme'
      ),
      'category' => 'custom',
      'icon' => 'admin-post',
      'mode' => 'auto',
      'supports' => ['align' => false],
      'render_callback' => function () {
        if ($id = get_field('category')) {
          $category = new Category($id);
          $posts = $category->posts(3);
          $link = $category->link();
        } else {
          $posts = Post::latest(3);
          $link = Post::archiveLink();
        }

        echo component(static::$name, [
          'title' => get_field('title'),
          'link' => $link,
          'posts' => $posts,
        ]);
      },
    ]);
  }

  public static function registerFields()
  {
    $key = static::$name;
    $title = static::$title;

    acf_add_local_field_group([
      'key' => "block_{$key}",
      'title' => $title,
      'fields' => [
        [
          'key' => "field_{$key}_block",
          'name' => 'block',
          'message' => "<h2 style='margin: -0.5rem 0 0 0; font-size: 1rem;'>{$title}</h2>",
          'type' => 'message',
          'required' => true,
        ],
        [
          'key' => "field_{$key}_title",
          'name' => 'title',
          'label' => __('Title', 'theme'),
          'type' => 'text',
          'default_value' => __('Latest articles', 'theme'),
        ],
        [
          'key' => "field_{$key}_category",
          'name' => 'category',
          'label' => __('Category', 'theme'),
          'instructions' => __(
            'Select a category to only show articles from this category.',
            'theme'
          ),
          'type' => 'taxonomy',
          'field_type' => 'select',
          'allow_null' => true,
          'add_term' => false,
        ],
      ],
      'location' => [
        [
          [
            'param' => 'block',
            'operator' => '==',
            'value' => 'acf/' . static::$name,
          ],
        ],
      ],
    ]);
  }
}
