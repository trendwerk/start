<?php

namespace Theme;

use Theme\Traits\HasFields;
use VarnishPurger;

class GeneralContent
{
  use HasFields;

  public static string $name = 'general-content';
  public static string $title;

  public static function init(): void
  {
    static::$title = __('General content', 'theme');

    // Add the general content options page
    add_action('acf/init', [static::class, 'addOptionsPage']);

    // Register general content fields
    add_action('acf/init', [static::class, 'registerFields']);

    // Purge Varnish cache when general content is saved
    add_action('acf/save_post', [static::class, 'purgeOnSave']);
  }

  /**
   * Add the general content options page.
   */
  public static function addOptionsPage()
  {
    acf_add_options_page([
      'menu_slug' => static::$name,
      'menu_title' => __('General', 'theme'),
      'page_title' => static::$title,
      'icon_url' => 'dashicons-welcome-widgets-menus',
    ]);
  }

  /**
   * Register general content fields.
   */
  public static function registerFields()
  {
    $key = static::$name;

    acf_add_local_field_group([
      'key' => static::$name,
      'title' => static::$title,
      'fields' => array_merge(static::ctaFields($key, true)),
      'instruction_placement' => 'field',
      'location' => [
        [
          [
            'param' => 'options_page',
            'operator' => '==',
            'value' => static::$name,
          ],
        ],
      ],
    ]);
  }

  /**
   * Purge Varnish cache when general content is saved.
   */
  public static function purgeOnSave()
  {
    $screen = get_current_screen();

    if ($screen->id == 'toplevel_page_' . static::$name) {
      $all = home_url() . '/?vhp-regex';
      VarnishPurger::purge_url($all);
    }
  }
}
