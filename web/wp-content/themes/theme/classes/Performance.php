<?php

namespace Theme;

class Performance
{
  public static function init(): void
  {
    // Prerender internal URLs when links are hovered
    add_filter('wp_speculation_rules_configuration', [
      static::class,
      'prerenderLinks',
    ]);
  }

  /**
   * Set speculation rules to moderate to prerender internal URLs when links are hovered.
   *
   * @param array|null $config
   * @return array|null
   */
  public static function prerenderLinks($config)
  {
    if (is_array($config)) {
      $config['mode'] = 'prerender';
      $config['eagerness'] = 'moderate';
    }

    return $config;
  }
}
