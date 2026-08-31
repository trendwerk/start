<?php

namespace Theme\PostTypes;

use Theme\Traits\HasFields;
use Theme\Traits\IsPost;

class Page
{
  use IsPost;
  use HasFields;

  public static string $postType = 'page';
  public static array $labels;

  public static function init(): void
  {
    static::$labels = [
      'name' => __('Pages', 'theme'),
      'singular_name' => __('Page', 'theme'),
    ];

    // Register page fields
    add_action('acf/init', [static::class, 'registerFields']);
  }

  /**
   * Register page fields.
   */
  public static function registerFields()
  {
    $key = static::$postType;

    acf_add_local_field_group([
      'key' => "{$key}_settings",
      'title' =>
        static::$labels['singular_name'] . ' ' . __('settings', 'theme'),
      'fields' => array_merge(
        static::headerFields($key),
        static::ctaFields($key)
      ),
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
}
