<?php

namespace Theme;

class Editor
{
  public static array $allowedBlockTypes = [
    // Text
    'core/heading',
    'core/list-item',
    'core/list',
    'core/paragraph',
    'core/quote',
    'core/table',

    // Media
    'core/audio',
    'core/gallery',
    'core/image',
    'core/video',

    // Design
    'core/button',
    'core/buttons',
    'core/columns',
    'core/separator',
    'core/spacer',
    'core/html',

    // Custom
    'acf/latest-posts',

    // Embeds
    'core/embed',
  ];

  public static function init(): void
  {
    // Limit the available block types
    add_filter(
      'allowed_block_types_all',
      [static::class, 'setAllowedBlockTypes'],
      10,
      2
    );

    // Add a custom block category
    add_filter('block_categories_all', [static::class, 'setBlockCategories']);
  }

  /**
   * Limit the available block types.
   *
   * @param bool|string[] $allowed
   * @param object $context
   * @return string[]
   */
  public static function setAllowedBlockTypes($allowed, $context)
  {
    return static::$allowedBlockTypes;
  }

  /**
   * Add a custom block category.
   *
   * @param array $categories
   * @return array
   */
  public static function setBlockCategories($categories)
  {
    $text = $categories[0];
    $media = $categories[1];
    $design = $categories[2];
    $custom = [
      'slug' => 'custom',
      'title' => __('Custom', 'theme'),
    ];
    $rest = array_slice($categories, 3);

    return array_merge([$text, $media, $design, $custom], $rest);
  }
}
