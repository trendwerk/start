<?php

namespace Theme;

class Excerpt
{
  public static function init(): void
  {
    // Set the excerpt length
    add_filter('excerpt_length', [static::class, 'setLength']);

    // Set the excerpt more string
    add_filter('excerpt_more', [static::class, 'setMore']);
  }

  /**
   * Set the excerpt length.
   *
   * @return int
   */
  public static function setLength()
  {
    return 25;
  }

  /**
   * Set the excerpt more string.
   *
   * @return string
   */
  public static function setMore()
  {
    return '&hellip;';
  }
}
