<?php

namespace Theme\Plugins;

class Acf
{
  public static function init(): void
  {
    add_filter('acf/blocks/default_block_version', [static::class, 'defaultBlockVersion']);
  }

  public static function defaultBlockVersion(int $version): int
  {
    return 3;
  }
}
