<?php

namespace Theme;

class Translation
{
  public static function init(): void
  {
    // Load the theme textdomain
    add_action('after_setup_theme', [static::class, 'loadTextdomain']);
  }

  /**
   * Load the theme textdomain.
   */
  public static function loadTextdomain()
  {
    load_theme_textdomain('theme', get_template_directory() . '/languages');
  }
}
